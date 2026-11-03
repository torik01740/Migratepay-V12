<?php

namespace Tests\Feature\Domain;

use App\Models\Deal;
use App\Models\User;
use App\Services\DealService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DealEscrowWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected DealService $dealService;
    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        $this->dealService = app(DealService::class);
        $this->walletService = app(WalletService::class);
    }

    public function test_buyer_initiated_deal_locks_escrow_atomically(): void
    {
        $buyer = User::factory()->create(['wallet_balance' => '2000.00']);
        $seller = User::factory()->create(['wallet_balance' => '0.00']);

        // Amount = 1000. 5% charge = 50. Shared 50% = Buyer pays 1025.00, Seller gets 975.00
        $response = $this->actingAs($buyer)->postJson('/api/deals', [
            'title'              => 'Web Development Deal',
            'amount'             => '1000.00',
            'counterparty_email' => $seller->email,
            'initiator_role'     => 'buyer',
            'charge_payer'       => 'shared_50',
            'deadline'           => now()->addDays(7)->toDateString(),
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('deal.status', 'awaiting_acceptance');
        $response->assertJsonPath('deal.buyer_pays', '1025.00');
        $response->assertJsonPath('deal.seller_gets', '975.00');

        // Buyer balance must be debited by 1025.00: 2000 - 1025 = 975.00
        $this->assertEquals('975.00', $buyer->fresh()->wallet_balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $buyer->id,
            'type'    => 'escrow_lock',
            'amount'  => '1025.00',
        ]);
    }

    public function test_counterparty_accepting_deal_secures_funds(): void
    {
        $buyer = User::factory()->create(['wallet_balance' => '2000.00']);
        $seller = User::factory()->create(['wallet_balance' => '0.00']);

        $deal = $this->dealService->createDeal($buyer, [
            'title'              => 'Mobile App UI',
            'amount'             => '1000.00',
            'counterparty_email' => $seller->email,
            'initiator_role'     => 'buyer',
            'charge_payer'       => 'shared_50',
        ]);

        $this->assertEquals('awaiting_acceptance', $deal->status);

        // Seller accepts deal
        $response = $this->actingAs($seller)->postJson("/api/deals/{$deal->id}/accept");
        $response->assertStatus(200);
        $response->assertJsonPath('deal.status', 'funds_secured');

        $this->assertEquals('funds_secured', $deal->fresh()->status);
    }

    public function test_counterparty_declining_deal_refunds_buyer_escrow(): void
    {
        $buyer = User::factory()->create(['wallet_balance' => '2000.00']);
        $seller = User::factory()->create(['wallet_balance' => '0.00']);

        $deal = $this->dealService->createDeal($buyer, [
            'title'              => 'Logo Design',
            'amount'             => '1000.00',
            'counterparty_email' => $seller->email,
            'initiator_role'     => 'buyer',
            'charge_payer'       => 'shared_50',
        ]);

        $this->assertEquals('975.00', $buyer->fresh()->wallet_balance);

        // Seller declines deal
        $response = $this->actingAs($seller)->postJson("/api/deals/{$deal->id}/decline");
        $response->assertStatus(200);
        $response->assertJsonPath('deal.status', 'declined');

        // Buyer balance restored: 975 + 1025 = 2000.00
        $this->assertEquals('2000.00', $buyer->fresh()->wallet_balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $buyer->id,
            'type'    => 'escrow_refund',
            'amount'  => '1025.00',
        ]);
    }

    public function test_seller_initiates_deal_and_buyer_accepts_funds_escrow(): void
    {
        $seller = User::factory()->create(['wallet_balance' => '0.00']);
        $buyer = User::factory()->create(['wallet_balance' => '1500.00']);

        $deal = $this->dealService->createDeal($seller, [
            'title'              => 'Consulting Services',
            'amount'             => '1000.00',
            'counterparty_email' => $buyer->email,
            'initiator_role'     => 'seller',
            'charge_payer'       => 'shared_50',
        ]);

        // Seller is initiator, so seller balance is NOT debited
        $this->assertEquals('0.00', $seller->fresh()->wallet_balance);
        $this->assertEquals('1500.00', $buyer->fresh()->wallet_balance);

        // Buyer accepts deal -> buyer balance is debited by 1025.00
        $response = $this->actingAs($buyer)->postJson("/api/deals/{$deal->id}/accept");
        $response->assertStatus(200);
        $response->assertJsonPath('deal.status', 'funds_secured');

        $this->assertEquals('475.00', $buyer->fresh()->wallet_balance); // 1500 - 1025 = 475
    }

    public function test_buyer_releases_funds_to_seller_atomically(): void
    {
        $buyer = User::factory()->create(['wallet_balance' => '2000.00']);
        $seller = User::factory()->create(['wallet_balance' => '0.00']);

        $deal = $this->dealService->createDeal($buyer, [
            'title'              => 'Final Release Project',
            'amount'             => '1000.00',
            'counterparty_email' => $seller->email,
            'initiator_role'     => 'buyer',
            'charge_payer'       => 'shared_50',
        ]);

        $this->dealService->acceptDeal($deal->id, $seller);
        $this->assertEquals('funds_secured', $deal->fresh()->status);

        // Buyer releases funds
        $response = $this->actingAs($buyer)->postJson("/api/deals/{$deal->id}/release");
        $response->assertStatus(200);
        $response->assertJsonPath('deal.status', 'completed');
        $response->assertJsonPath('wallet_balance', '975.00');

        $this->assertEquals('975.00', $seller->fresh()->wallet_balance);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $seller->id,
            'type'    => 'escrow_release',
            'amount'  => '975.00',
        ]);
    }

    public function test_deliverable_submission_and_buyer_review(): void
    {
        $buyer = User::factory()->create(['wallet_balance' => '2000.00']);
        $seller = User::factory()->create(['wallet_balance' => '0.00']);

        $deal = $this->dealService->createDeal($buyer, [
            'title'              => 'Design Package',
            'amount'             => '1000.00',
            'counterparty_email' => $seller->email,
            'initiator_role'     => 'buyer',
            'charge_payer'       => 'shared_50',
        ]);
        $this->dealService->acceptDeal($deal->id, $seller);

        $file = UploadedFile::fake()->create('final_design.pdf', 500);

        // Seller submits work
        $subResponse = $this->actingAs($seller)->postJson("/api/deals/{$deal->id}/submit", [
            'file' => $file,
        ]);
        $subResponse->assertStatus(200);

        // Buyer reviews work and approves
        $reviewResponse = $this->actingAs($buyer)->postJson("/api/deals/{$deal->id}/review-submission", [
            'decision' => 'approved',
        ]);
        $reviewResponse->assertStatus(200);
        $this->assertEquals('buyer_review', $deal->fresh()->status);
    }
}
