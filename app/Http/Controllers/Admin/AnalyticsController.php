<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Admin: Master Transaction Ledger.
     */
    public function getLedger(): JsonResponse
    {
        $transactions = Transaction::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($txn) {
                return [
                    'id'          => 'TXN' . str_pad($txn->id, 6, '0', STR_PAD_LEFT),
                    'date'        => $txn->created_at->format('Y-m-d H:i:s'),
                    'user'        => $txn->user ? "{$txn->user->name} ({$txn->user->email})" : 'System',
                    'type'        => $txn->type,
                    'description' => $txn->description ?: 'Transaction',
                    'amount'      => $txn->amount,
                    'balance'     => $txn->balance_after,
                ];
            });

        return response()->json($transactions);
    }

    /**
     * Admin: Dashboard metrics and volume analytics.
     */
    public function getDashboardMetrics(): JsonResponse
    {
        $currentYear = date('Y');

        // Monthly completed volume
        $deals = Deal::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->get();

        $monthlyVolumeData = $deals->groupBy(function ($deal) {
            return Carbon::parse($deal->completed_at)->format('n');
        })->map(function ($group) {
            return $group->sum('amount');
        });

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $barData = [];
        $totalVolume = '0.00';

        for ($i = 1; $i <= 12; $i++) {
            $val = isset($monthlyVolumeData[$i]) ? (float)$monthlyVolumeData[$i] : 0.0;
            $barData[] = [
                'month' => $months[$i - 1],
                'val'   => $val,
            ];
        }

        $totalUsers = User::count();
        $verifiedUsers = User::where('verification_status', 'VERIFIED')->count();
        $totalDeals = Deal::count();
        $completedDeals = Deal::where('status', 'completed')->count();
        $disputedDeals = Deal::where('status', 'disputed')->count();

        $successRate = $totalDeals > 0 ? round(($completedDeals / $totalDeals) * 100, 1) : 0;
        $disputeRate = $totalDeals > 0 ? round(($disputedDeals / $totalDeals) * 100, 1) : 0;

        $maleCount = User::whereRaw('LOWER(gender) = ?', ['male'])->count();
        $femaleCount = User::whereRaw('LOWER(gender) = ?', ['female'])->count();
        $otherCount = User::whereNotIn(DB::raw('LOWER(gender)'), ['male', 'female'])->orWhereNull('gender')->count();

        return response()->json([
            'barData' => $barData,
            'metrics' => [
                'monthlyVolume'   => $totalVolume,
                'activeUsers'     => $totalUsers,
                'dealSuccessRate' => $successRate,
                'disputeRate'     => $disputeRate,
            ],
            'volume' => [
                'chart_data'   => $barData,
                'total_deals'  => $totalDeals,
                'success_rate' => $successRate,
                'dispute_rate' => $disputeRate,
            ],
            'users' => [
                'total_users'    => $totalUsers,
                'verified_users' => $verifiedUsers,
                'demographics'   => [
                    'male'   => $maleCount,
                    'female' => $femaleCount,
                    'other'  => $otherCount,
                ],
            ],
        ]);
    }
}
