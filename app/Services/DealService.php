<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Deal;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DealService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Get charge percentage tier based on amount.
     */
    public function getChargePercentage(string $amount): string
    {
        $amt = DecimalSafeMath::canonicalize($amount, 2);

        if (DecimalSafeMath::comp($amt, '1000.00') >= 0 && DecimalSafeMath::comp($amt, '5000.00') <= 0) {
            return '5.00';
        }
        if (DecimalSafeMath::comp($amt, '5000.00') > 0 && DecimalSafeMath::comp($amt, '10000.00') <= 0) {
            return '4.50';
        }
        if (DecimalSafeMath::comp($amt, '10000.00') > 0 && DecimalSafeMath::comp($amt, '15000.00') <= 0) {
            return '4.00';
        }
        if (DecimalSafeMath::comp($amt, '15000.00') > 0) {
            return '3.00';
        }
        return '5.00';
    }

    /**
     * Calculate transaction charge, coupons, and settlement split with exact decimal arithmetic.
     */
    public function calculateCharge(
        string $amount,
        string $chargePayer = 'shared_50',
        ?string $couponCode = null,
        ?User $user = null,
        string $sellerPct = '0',
        string $buyerPct = '0'
    ): array {
        $cleanAmount = DecimalSafeMath::canonicalize($amount, 2);
        $chargePct = $this->getChargePercentage($cleanAmount);

        // originalCharge = amount * (chargePct / 100)
        $pctFraction = DecimalSafeMath::div($chargePct, '100.00', 4);
        $originalCharge = DecimalSafeMath::mul($cleanAmount, $pctFraction, 2);
        $totalCharge = $originalCharge;

        $coupon = null;
        $couponDiscount = '0.00';
        $couponError = null;

        if (!empty($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon) {
                if ($coupon->isValidForUse()) {
                    if ($user) {
                        $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                            ->where('user_id', $user->id)
                            ->count();
                        if (!$coupon->usage_per_user || $userUsage < $coupon->usage_per_user) {
                            if ($coupon->discount_type === 'percentage') {
                                $discFraction = DecimalSafeMath::div((string)$coupon->discount_value, '100.00', 4);
                                $couponDiscount = DecimalSafeMath::mul($totalCharge, $discFraction, 2);
                            } else {
                                $couponDiscount = DecimalSafeMath::canonicalize($coupon->discount_value, 2);
                            }
                            if (DecimalSafeMath::comp($couponDiscount, $totalCharge) > 0) {
                                $couponDiscount = $totalCharge;
                            }
                            $totalCharge = DecimalSafeMath::sub($totalCharge, $couponDiscount, 2);
                        } else {
                            $couponError = 'You have reached the usage limit for this coupon.';
                        }
                    } else {
                        if ($coupon->discount_type === 'percentage') {
                            $discFraction = DecimalSafeMath::div((string)$coupon->discount_value, '100.00', 4);
                            $couponDiscount = DecimalSafeMath::mul($totalCharge, $discFraction, 2);
                        } else {
                            $couponDiscount = DecimalSafeMath::canonicalize($coupon->discount_value, 2);
                        }
                        if (DecimalSafeMath::comp($couponDiscount, $totalCharge) > 0) {
                            $couponDiscount = $totalCharge;
                        }
                        $totalCharge = DecimalSafeMath::sub($totalCharge, $couponDiscount, 2);
                    }
                } else {
                    $couponError = 'Coupon is invalid or expired.';
                }
            } else {
                $couponError = 'Coupon not found.';
            }
        }

        $sellerCharge = '0.00';
        $buyerCharge = '0.00';

        switch ($chargePayer) {
            case 'seller_100':
                $sellerCharge = $totalCharge;
                break;
            case 'buyer_100':
                $buyerCharge = $totalCharge;
                break;
            case 'shared_50':
                $sellerCharge = DecimalSafeMath::div($totalCharge, '2.00', 2);
                $buyerCharge = DecimalSafeMath::sub($totalCharge, $sellerCharge, 2);
                break;
            case 'custom':
                $sFraction = DecimalSafeMath::div($sellerPct, '100.00', 4);
                $sellerCharge = DecimalSafeMath::mul($totalCharge, $sFraction, 2);
                $buyerCharge = DecimalSafeMath::sub($totalCharge, $sellerCharge, 2);
                break;
        }

        $buyerSettlement = DecimalSafeMath::add($cleanAmount, $buyerCharge, 2);
        $sellerSettlement = DecimalSafeMath::sub($cleanAmount, $sellerCharge, 2);

        $tiers = [
            ['range' => '৳1,000 – ৳5,000', 'charge' => '5%'],
            ['range' => '৳5,001 – ৳10,000', 'charge' => '4.5%'],
            ['range' => '৳10,001 – ৳15,000', 'charge' => '4%'],
            ['range' => '৳15,001+', 'charge' => '3%'],
        ];

        return [
            'amount'            => $cleanAmount,
            'charge_percentage' => $chargePct,
            'original_charge'   => $originalCharge,
            'coupon_discount'   => $couponDiscount,
            'total_charge'      => $totalCharge,
            'buyer_charge'      => $buyerCharge,
            'seller_charge'     => $sellerCharge,
            'buyer_settlement'  => $buyerSettlement,
            'seller_settlement' => $sellerSettlement,
            'tiers'             => $tiers,
            'coupon_error'      => $couponError,
            'coupon_applied'    => $coupon && !$couponError,
            'coupon_model'      => ($coupon && !$couponError) ? $coupon : null,
        ];
    }

    /**
     * Create a new deal and atomically lock escrow if initiator is the buyer.
     */
    public function createDeal(User $user, array $data): Deal
    {
        $counterpartyEmail = strtolower(trim($data['counterparty_email']));

        if ($counterpartyEmail === strtolower(trim($user->email))) {
            throw new RuntimeException('You cannot initiate a deal with yourself.');
        }

        $dealAmount = DecimalSafeMath::canonicalize($data['amount'], 2);
        if (DecimalSafeMath::comp($dealAmount, '0.00') <= 0) {
            throw new RuntimeException('Deal amount must be greater than zero.');
        }

        $chargePayer = $data['charge_payer'] ?? 'shared_50';
        $sellerPct = (string)($data['seller_pct'] ?? '0');
        $buyerPct = (string)($data['buyer_pct'] ?? '0');

        if ($chargePayer === 'custom') {
            $sumPct = DecimalSafeMath::add($sellerPct, $buyerPct, 2);
            if (DecimalSafeMath::comp($sumPct, '100.00') !== 0) {
                throw new RuntimeException('Custom charge percentages must equal 100%.');
            }
        }

        $calc = $this->calculateCharge(
            $dealAmount,
            $chargePayer,
            $data['coupon_code'] ?? null,
            $user,
            $sellerPct,
            $buyerPct
        );

        $buyerPays = $calc['buyer_settlement'];
        $sellerGets = $calc['seller_settlement'];
        $totalCharge = $calc['total_charge'];
        $originalCharge = $calc['original_charge'];
        $couponDiscount = $calc['coupon_discount'];
        $coupon = $calc['coupon_model'];

        return DB::transaction(function () use (
            $user, $data, $counterpartyEmail, $dealAmount, $buyerPays,
            $sellerGets, $totalCharge, $originalCharge, $couponDiscount, $coupon
        ) {
            $counterparty = User::where('email', $counterpartyEmail)->first();

            $deal = Deal::create([
                'deal_id'            => 'DX-' . mt_rand(100000, 999999),
                'title'              => $data['title'],
                'category_id'        => $data['category_id'] ?? null,
                'amount'             => $dealAmount,
                'buyer_pays'         => $buyerPays,
                'seller_gets'        => $sellerGets,
                'charge_total'       => $totalCharge,
                'initiator_id'       => $user->id,
                'counterparty_id'    => $counterparty?->id,
                'counterparty_email' => $counterpartyEmail,
                'initiator_role'     => $data['initiator_role'],
                'status'             => 'awaiting_acceptance',
                'deadline'           => $data['deadline'] ?? now()->addDays(7)->toDateString(),
                'workspace'          => [
                    'messages'        => [],
                    'submissions'     => [],
                    'extensions'      => [],
                    'custom_category' => $data['custom_category'] ?? null,
                ],
                'coupon_id'          => $coupon?->id,
                'original_charge'    => $coupon ? $originalCharge : null,
                'coupon_discount'    => $coupon ? $couponDiscount : null,
            ]);

            // If initiator is buyer, lock funds atomically
            if ($data['initiator_role'] === 'buyer') {
                $this->walletService->lockForEscrow(
                    $user,
                    $buyerPays,
                    $deal,
                    "Escrow locked for deal: {$data['title']}"
                );
            }

            if ($coupon) {
                CouponUsage::create([
                    'coupon_id'       => $coupon->id,
                    'user_id'         => $user->id,
                    'deal_id'         => $deal->id,
                    'original_charge' => $originalCharge,
                    'discount_amount' => $couponDiscount,
                    'final_charge'    => $totalCharge,
                ]);
                $coupon->increment('current_usage');
            }

            if ($counterparty) {
                Notification::create([
                    'user_id'        => $counterparty->id,
                    'type'           => 'deal_invitation',
                    'title'          => 'New Deal Invitation',
                    'message'        => "{$user->name} ({$user->email}) invited you to deal: {$data['title']} (৳{$dealAmount}).",
                    'reference_type' => Deal::class,
                    'reference_id'   => $deal->id,
                ]);
            }

            return $deal->fresh(['initiator', 'counterparty']);
        });
    }

    /**
     * Get user deals.
     */
    public function getUserDeals(User $user)
    {
        return Deal::with(['initiator', 'counterparty', 'category'])
            ->where(function ($query) use ($user) {
                $query->where('initiator_id', $user->id)
                    ->orWhere('counterparty_id', $user->id)
                    ->orWhere('counterparty_email', strtolower($user->email));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get single deal ensuring user is a party or administrator.
     */
    public function getDealForUser(int $id, User $user): Deal
    {
        $deal = Deal::with(['initiator', 'counterparty', 'category'])->findOrFail($id);

        if (!$this->isParty($deal, $user) && $user->role !== 'admin') {
            throw new RuntimeException('You are not a party to this deal.');
        }

        return $deal;
    }

    /**
     * Counterparty accepts the deal. If counterparty is the buyer, funds are locked atomically.
     */
    public function acceptDeal(int $id, User $user): Deal
    {
        return DB::transaction(function () use ($id, $user) {
            $deal = Deal::lockForUpdate()->findOrFail($id);

            if ($deal->status !== 'awaiting_acceptance') {
                throw new RuntimeException('This deal is not awaiting acceptance.');
            }

            if (
                $deal->counterparty_id !== $user->id &&
                strtolower($deal->counterparty_email) !== strtolower($user->email)
            ) {
                throw new RuntimeException('You are not the counterparty of this deal.');
            }

            if ($deal->initiator_role === 'seller') {
                // Counterparty is buyer and needs to fund escrow
                $buyerPays = DecimalSafeMath::canonicalize($deal->buyer_pays, 2);
                $this->walletService->lockForEscrow(
                    $user,
                    $buyerPays,
                    $deal,
                    "Deal accepted - escrow funded: {$deal->title}"
                );
            }

            $deal->counterparty_id = $user->id;
            $deal->status = 'funds_secured';
            $deal->accepted_at = now();
            $deal->save();

            if ($deal->initiator_id) {
                Notification::create([
                    'user_id'        => $deal->initiator_id,
                    'type'           => 'deal_accepted',
                    'title'          => 'Deal Accepted',
                    'message'        => "Your deal '{$deal->title}' was accepted by {$user->name}. Funds are secured in escrow.",
                    'reference_type' => Deal::class,
                    'reference_id'   => $deal->id,
                ]);
            }

            return $deal->fresh(['initiator', 'counterparty']);
        });
    }

    /**
     * Counterparty declines the deal. If initiator funded escrow, funds are atomically refunded.
     */
    public function declineDeal(int $id, User $user): Deal
    {
        return DB::transaction(function () use ($id, $user) {
            $deal = Deal::lockForUpdate()->findOrFail($id);

            if ($deal->status !== 'awaiting_acceptance') {
                throw new RuntimeException('This deal is no longer awaiting acceptance.');
            }

            if (
                $deal->counterparty_id !== $user->id &&
                strtolower($deal->counterparty_email) !== strtolower($user->email)
            ) {
                throw new RuntimeException('You are not the counterparty of this deal.');
            }

            // Refund initiator if initiator was buyer who locked escrow
            if ($deal->initiator_role === 'buyer' && $deal->initiator) {
                $buyerPays = DecimalSafeMath::canonicalize($deal->buyer_pays, 2);
                $this->walletService->refundEscrow(
                    $deal->initiator,
                    $buyerPays,
                    $deal,
                    "Deal declined by counterparty - escrow refunded: {$deal->title}"
                );
            }

            $deal->counterparty_id = $user->id;
            $deal->status = 'declined';
            $deal->save();

            if ($deal->initiator_id) {
                Notification::create([
                    'user_id'        => $deal->initiator_id,
                    'type'           => 'deal_declined',
                    'title'          => 'Deal Declined',
                    'message'        => "Your deal invitation '{$deal->title}' was declined by the counterparty.",
                    'reference_type' => Deal::class,
                    'reference_id'   => $deal->id,
                ]);
            }

            return $deal->fresh(['initiator', 'counterparty']);
        });
    }

    /**
     * Buyer releases funds to seller upon satisfactory work.
     */
    public function releaseFunds(int $id, User $user): array
    {
        return DB::transaction(function () use ($id, $user) {
            $deal = Deal::lockForUpdate()->findOrFail($id);

            // Explicit allowlist of valid source states for buyer fund release
            $permittedStates = ['funds_secured', 'buyer_review'];
            if (!in_array($deal->status, $permittedStates, true)) {
                if (in_array($deal->status, ['completed', 'released'], true)) {
                    throw new RuntimeException('Funds already released.');
                }
                throw new RuntimeException("Funds cannot be released at this stage (current status: '{$deal->status}').");
            }

            if (!$this->isBuyer($deal, $user)) {
                throw new RuntimeException('Only the buyer can release funds.');
            }

            $seller = $this->getSeller($deal);
            if (!$seller) {
                throw new RuntimeException('Seller account not found. Cannot release funds.');
            }

            $sellerGets = DecimalSafeMath::canonicalize($deal->seller_gets, 2);

            $transaction = $this->walletService->releaseEscrow(
                $seller,
                $sellerGets,
                $deal,
                "Deal completed - funds released: {$deal->title}"
            );

            $deal->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            Notification::create([
                'user_id'        => $seller->id,
                'type'           => 'funds_received',
                'title'          => 'Funds Received',
                'message'        => "৳{$sellerGets} has been released for deal: {$deal->title}.",
                'reference_type' => Deal::class,
                'reference_id'   => $deal->id,
            ]);

            return [
                'deal'           => $deal->fresh(['initiator', 'counterparty']),
                'wallet_balance' => $this->walletService->getBalance($seller->id),
                'transaction'    => $transaction,
            ];
        });
    }

    /**
     * Admin approves fund release for deal.
     */
    public function adminApproveFunds(int $id, User $admin): array
    {
        return DB::transaction(function () use ($id, $admin) {
            $deal = Deal::lockForUpdate()->findOrFail($id);

            // Workstream B: Explicit allowlist of valid source states for admin fund release
            $permittedStates = ['funds_secured', 'buyer_review', 'disputed'];
            if (!in_array($deal->status, $permittedStates, true)) {
                if (in_array($deal->status, ['completed', 'released'], true)) {
                    throw new RuntimeException('Funds already released.');
                }
                throw new RuntimeException("Cannot release funds: deal is currently in '{$deal->status}' status. Escrow funds must be in " . implode(', ', $permittedStates) . ".");
            }

            if ($deal->admin_approved) {
                throw new RuntimeException('Funds already approved.');
            }

            $seller = $this->getSeller($deal);
            if (!$seller) {
                throw new RuntimeException('Seller not found.');
            }

            $sellerGets = DecimalSafeMath::canonicalize($deal->seller_gets, 2);

            $transaction = $this->walletService->releaseEscrow(
                $seller,
                $sellerGets,
                $deal,
                "Admin approved funds release: {$deal->title}"
            );

            $deal->update([
                'admin_approved' => true,
                'status'         => 'completed',
                'completed_at'   => now(),
            ]);

            Notification::create([
                'user_id'        => $seller->id,
                'type'           => 'funds_received',
                'title'          => 'Funds Released by Admin',
                'message'        => "৳{$sellerGets} has been released for: {$deal->title}.",
                'reference_type' => Deal::class,
                'reference_id'   => $deal->id,
            ]);

            return [
                'deal'           => $deal->fresh(['initiator', 'counterparty']),
                'wallet_balance' => $this->walletService->getBalance($seller->id),
                'transaction'    => $transaction,
            ];
        });
    }

    /**
     * Submit work or deliverables for a deal into private storage.
     */
    public function submitWorkDeliverable(int $id, User $user, array $files = [], ?string $fileName = null, ?string $fileSize = null): Deal
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        if ($deal->status !== 'funds_secured') {
            throw new RuntimeException('Deal is not active.');
        }

        $workspace = $deal->workspace ?? ['messages' => [], 'submissions' => [], 'extensions' => []];

        if (!empty($files)) {
            foreach ($files as $file) {
                $path = $file->store("deals/{$deal->id}/deliverables", 'private');
                $size = number_format($file->getSize() / 1024 / 1024, 2) . ' MB';

                $workspace['submissions'][] = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $size,
                    'status'    => 'Pending Approval',
                    'by'        => $user->email,
                    'at'        => now()->toISOString(),
                ];
            }
        } elseif ($fileName) {
            $workspace['submissions'][] = [
                'file_name' => $fileName,
                'file_size' => $fileSize ?? 'N/A',
                'file_path' => null,
                'status'    => 'Pending Approval',
                'by'        => $user->email,
                'at'        => now()->toISOString(),
            ];
        } else {
            throw new RuntimeException('No files provided.');
        }

        $deal->workspace = $workspace;
        $deal->save();

        return $deal;
    }

    /**
     * Buyer reviews submission.
     */
    public function reviewSubmission(int $id, User $user, string $decision): array
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        if ($deal->status !== 'funds_secured') {
            throw new RuntimeException('Deal is not active.');
        }

        if (!$this->isBuyer($deal, $user)) {
            throw new RuntimeException('Only buyer can review submissions.');
        }

        $workspace = $deal->workspace ?? ['messages' => [], 'submissions' => [], 'extensions' => []];

        if ($decision === 'approved') {
            foreach ($workspace['submissions'] as &$sub) {
                if ($sub['status'] === 'Pending Approval') {
                    $sub['status'] = 'Approved';
                }
            }

            $deal->update([
                'status'         => 'buyer_review',
                'buyer_accepted' => true,
                'workspace'      => $workspace,
            ]);

            return [
                'message' => 'Project accepted. Funds ready for release.',
                'deal'    => $deal,
            ];
        } else {
            foreach ($workspace['submissions'] as &$sub) {
                if ($sub['status'] === 'Pending Approval') {
                    $sub['status'] = 'Rejected';
                }
            }

            $deal->update([
                'status'    => 'disputed',
                'workspace' => $workspace,
            ]);

            return [
                'message' => 'Project rejected. Deal marked as disputed.',
                'deal'    => $deal,
            ];
        }
    }

    /**
     * Send workspace chat message.
     */
    public function sendWorkspaceMessage(int $id, User $user, string $text): Deal
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        $workspace = $deal->workspace ?? ['messages' => [], 'submissions' => [], 'extensions' => []];
        $workspace['messages'][] = [
            'sender'      => $user->email,
            'sender_name' => $user->name,
            'text'        => $text,
            'at'          => now()->toISOString(),
        ];

        $deal->workspace = $workspace;
        $deal->save();

        return $deal;
    }

    /**
     * Request deadline extension.
     */
    public function requestExtension(int $id, User $user, int $days): Deal
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        $workspace = $deal->workspace ?? ['messages' => [], 'submissions' => [], 'extensions' => []];
        $workspace['extensions'][] = [
            'days'   => $days,
            'status' => 'Pending',
            'by'     => $user->email,
            'at'     => now()->toISOString(),
        ];

        $deal->workspace = $workspace;
        $deal->save();

        return $deal;
    }

    /**
     * Buyer approves deadline extension.
     */
    public function approveExtension(int $id, User $user, int $extensionIndex): Deal
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        if (!$this->isBuyer($deal, $user)) {
            throw new RuntimeException('Only buyer can approve extensions.');
        }

        $workspace = $deal->workspace ?? ['messages' => [], 'submissions' => [], 'extensions' => []];

        if (!isset($workspace['extensions'][$extensionIndex])) {
            throw new RuntimeException('Extension not found.');
        }

        $ext = &$workspace['extensions'][$extensionIndex];
        $ext['status'] = 'Approved';

        $currentDeadline = Carbon::parse($deal->deadline);
        $deal->deadline = $currentDeadline->addDays($ext['days'])->toDateString();
        $deal->workspace = $workspace;
        $deal->save();

        return $deal;
    }

    /**
     * Request custom service for a deal (1% charge).
     */
    public function requestCustomService(int $id, User $user): array
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        if (!$this->isBuyer($deal, $user)) {
            throw new RuntimeException('Only buyer can request custom service.');
        }

        if ($deal->custom_service) {
            throw new RuntimeException('Custom service already requested.');
        }

        $charge = DecimalSafeMath::mul($deal->amount, '0.01', 2);

        $deal->update([
            'custom_service'        => true,
            'custom_service_charge' => $charge,
        ]);

        return [
            'charge' => $charge,
            'deal'   => $deal,
        ];
    }

    /**
     * Report an issue in a deal.
     */
    public function reportIssue(int $id, User $user, array $data): Report
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user)) {
            throw new RuntimeException('Not authorized.');
        }

        $report = Report::create([
            'deal_id'     => $deal->id,
            'reported_by' => $user->id,
            'reason'      => $data['reason'],
            'description' => $data['description'] ?? '',
            'status'      => 'open',
        ]);

        return $report->load('reporter');
    }

    /**
     * Get reports for a deal.
     */
    public function getReports(int $id, User $user)
    {
        $deal = Deal::findOrFail($id);

        if (!$this->isParty($deal, $user) && $user->role !== 'admin') {
            throw new RuntimeException('Not authorized.');
        }

        return Report::with('reporter')
            ->where('deal_id', $deal->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Admin: Resolve a report.
     */
    public function resolveReport(int $id, User $admin, string $resolutionNote): Report
    {
        $report = Report::findOrFail($id);

        $report->update([
            'status'          => 'resolved',
            'resolution_note' => $resolutionNote,
            'resolved_by'     => $admin->id,
            'resolved_at'     => now(),
        ]);

        return $report;
    }

    /**
     * Admin: List all deals with optional status filter.
     */
    public function getAdminDeals(?string $status = null)
    {
        $query = Deal::with(['initiator', 'counterparty', 'category'])->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['awaiting_acceptance', 'funds_secured', 'released', 'completed', 'disputed', 'buyer_review'])) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Admin: List all reports.
     */
    public function getAdminReports(?string $status = null)
    {
        $query = Report::with(['deal', 'reporter', 'resolver'])->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['open', 'resolved'])) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Check if user is a party to the deal.
     */
    public function isParty(Deal $deal, User $user): bool
    {
        return $deal->initiator_id === $user->id
            || $deal->counterparty_id === $user->id
            || strtolower(trim($deal->counterparty_email)) === strtolower(trim($user->email));
    }

    /**
     * Check if user is the buyer in the deal.
     */
    public function isBuyer(Deal $deal, User $user): bool
    {
        if ($deal->initiator_role === 'buyer') {
            return $deal->initiator_id === $user->id;
        }
        return $deal->counterparty_id === $user->id
            || strtolower(trim($deal->counterparty_email)) === strtolower(trim($user->email));
    }

    /**
     * Get the seller user model.
     */
    public function getSeller(Deal $deal): ?User
    {
        if ($deal->initiator_role === 'seller') {
            return $deal->initiator;
        }
        if ($deal->counterparty_id) {
            return $deal->counterparty;
        }
        return User::where('email', strtolower(trim($deal->counterparty_email)))->first();
    }

    /**
     * Get the buyer user ID.
     */
    public function getBuyerId(Deal $deal): ?int
    {
        if ($deal->initiator_role === 'buyer') {
            return $deal->initiator_id;
        }
        return $deal->counterparty_id;
    }
}
