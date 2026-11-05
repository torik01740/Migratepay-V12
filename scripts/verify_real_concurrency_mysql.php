<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Deposit;
use App\Models\WithdrawalRequest;
use App\Models\WithdrawalMethod;
use App\Models\Deal;
use App\Models\PaymentGateway;
use App\Models\PaymentWebhookIdempotency;
use App\Models\Transaction;
use App\Services\PaymentGatewayService;
use App\Services\DepositService;
use App\Services\WithdrawalService;
use App\Services\DealService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// If arguments given, execute worker action
if (isset($argv[1])) {
    $action = $argv[1];
    $id = $argv[2] ?? null;
    $ref = $argv[3] ?? null;

    $admin = User::where('role', 'admin')->first();

    try {
        if ($action === 'webhook') {
            $service = app(PaymentGatewayService::class);
            $res = $service->processCallback('bkash', [
                'payment_reference' => $ref,
                'paymentID' => $ref,
                'trxID' => 'TRX-' . $ref,
                'amount' => '100.00',
                'status' => 'success',
                'user_id' => $id,
                'customer_id' => $id,
            ]);
            echo json_encode(['worker' => getmypid(), 'status' => 'ok', 'result' => $res]);
        } elseif ($action === 'deposit') {
            $service = app(DepositService::class);
            $res = $service->approveDeposit((int)$id, $admin, 'Concurrent worker test');
            echo json_encode(['worker' => getmypid(), 'status' => 'ok', 'result' => $res]);
        } elseif ($action === 'withdrawal') {
            $service = app(WithdrawalService::class);
            $res = $service->approveWithdrawal((int)$id, $admin, 'Concurrent worker test');
            echo json_encode(['worker' => getmypid(), 'status' => 'ok', 'result' => $res]);
        } elseif ($action === 'deal') {
            $service = app(DealService::class);
            $res = $service->adminApproveFunds((int)$id, $admin);
            echo json_encode(['worker' => getmypid(), 'status' => 'ok', 'result' => $res]);
        }
    } catch (\Throwable $e) {
        echo json_encode(['worker' => getmypid(), 'status' => 'rejected', 'error' => $e->getMessage()]);
    }
    exit(0);
}

// ─────────────────────────────────────────────────────────────────────────────
// Master Test Orchestrator
// ─────────────────────────────────────────────────────────────────────────────

echo "=== MIGRATEPAY V12: REAL FINANCIAL CONCURRENCY VERIFICATION (MYSQL 8.0) ===\n";
echo "Active DB Connection: " . DB::connection()->getDriverName() . " / DB: " . DB::connection()->getDatabaseName() . "\n\n";

$phpBinary = PHP_BINARY;
$selfPath = __FILE__;

function runParallel(string $cmd1, string $cmd2): array {
    $descriptors = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $p1 = proc_open($cmd1, $descriptors, $pipes1);
    $p2 = proc_open($cmd2, $descriptors, $pipes2);

    $out1 = stream_get_contents($pipes1[1]);
    $err1 = stream_get_contents($pipes1[2]);
    fclose($pipes1[0]); fclose($pipes1[1]); fclose($pipes1[2]);

    $out2 = stream_get_contents($pipes2[1]);
    $err2 = stream_get_contents($pipes2[2]);
    fclose($pipes2[0]); fclose($pipes2[1]); fclose($pipes2[2]);

    $status1 = proc_close($p1);
    $status2 = proc_close($p2);

    return [
        'worker1' => json_decode($out1, true) ?: ['raw' => $out1, 'err' => $err1],
        'worker2' => json_decode($out2, true) ?: ['raw' => $out2, 'err' => $err2],
    ];
}

// Ensure bKash gateway exists
$gateway = PaymentGateway::firstOrCreate(
    ['slug' => 'bkash'],
    [
        'name' => 'bKash',
        'type' => 'automated',
        'is_active' => true,
        'currencies' => ['BDT'],
        'charge_percentage' => '0.00',
        'charge_fixed' => '0.00',
        'api_key' => 'test_api_key',
        'secret_key' => 'test_secret_key',
    ]
);

// Clean up and set up test customer
$testUser = User::firstOrCreate(
    ['email' => 'concurrency_test_customer@example.com'],
    [
        'name' => 'Concurrency Customer',
        'username' => 'concurrency_customer_' . substr(md5(uniqid()), 0, 6),
        'password' => bcrypt('Secret123!'),
        'role' => 'user',
        'wallet_balance' => '1000.00',
        'kyc_status' => 'approved',
    ]
);
$testUser->update(['wallet_balance' => '1000.00']);

$testSeller = User::firstOrCreate(
    ['email' => 'concurrency_test_seller@example.com'],
    [
        'name' => 'Concurrency Seller',
        'username' => 'concurrency_seller_' . substr(md5(uniqid()), 0, 6),
        'password' => bcrypt('Secret123!'),
        'role' => 'user',
        'wallet_balance' => '0.00',
        'kyc_status' => 'approved',
    ]
);
$testSeller->update(['wallet_balance' => '0.00']);

$admin = User::where('role', 'admin')->first();

$results = [];

// 1. Concurrent Webhook Callback Test
echo "[TEST 1/4] Running concurrent webhook calls with identical payment reference...\n";
$ref = 'RACE_PAY_' . Str::random(10);
$cmd1 = "\"$phpBinary\" \"$selfPath\" webhook {$testUser->id} $ref";
$cmd2 = "\"$phpBinary\" \"$selfPath\" webhook {$testUser->id} $ref";

$res1 = runParallel($cmd1, $cmd2);
$testUser->refresh();

$idempCount = PaymentWebhookIdempotency::where('gateway_slug', 'bkash')
    ->where('payment_reference', $ref)
    ->count();
$txnCount = Transaction::where('description', 'like', "%{$ref}%")->count();

$passed1 = ($testUser->wallet_balance == '1100.00' && $idempCount === 1 && $txnCount === 1);
$results['webhook'] = [
    'ref' => $ref,
    'user_final_balance' => (string)$testUser->wallet_balance,
    'expected_balance' => '1100.00', // Only 1x 100.00 credited
    'idempotency_records' => $idempCount,
    'transactions_created' => $txnCount,
    'worker_outputs' => $res1,
    'passed' => $passed1,
];
echo "  -> Result: " . ($passed1 ? "PASSED (Balance: {$testUser->wallet_balance}, Txns: 1, Idemp: 1)" : "FAILED") . "\n";


// 2. Concurrent Deposit Approval Test
echo "\n[TEST 2/4] Running concurrent deposit approvals for identical deposit...\n";
$dep = Deposit::create([
    'user_id' => $testUser->id,
    'amount' => '250.00',
    'gateway' => 'manual_bank',
    'status' => 'pending',
    'proof_note' => 'Concurrent test deposit',
    'admin_read' => true,
]);

$cmd1 = "\"$phpBinary\" \"$selfPath\" deposit {$dep->id}";
$cmd2 = "\"$phpBinary\" \"$selfPath\" deposit {$dep->id}";

$res2 = runParallel($cmd1, $cmd2);
$testUser->refresh();
$dep->refresh();

$depTxnCount = Transaction::where('reference_type', 'App\\Models\\Deposit')
    ->where('reference_id', $dep->id)
    ->count();

$passed2 = ($testUser->wallet_balance == '1350.00' && $dep->status === 'approved' && $depTxnCount === 1);
$results['deposit'] = [
    'deposit_id' => $dep->id,
    'status' => $dep->status,
    'user_final_balance' => (string)$testUser->wallet_balance,
    'expected_balance' => '1350.00', // 1100 + 250 = 1350, NOT 1600
    'transactions_created' => $depTxnCount,
    'worker_outputs' => $res2,
    'passed' => $passed2,
];
echo "  -> Result: " . ($passed2 ? "PASSED (Balance: {$testUser->wallet_balance}, Status: {$dep->status}, Txns: 1)" : "FAILED") . "\n";


// 3. Concurrent Withdrawal Approval Test
echo "\n[TEST 3/4] Running concurrent withdrawal approvals for identical withdrawal...\n";
$method = WithdrawalMethod::firstOrCreate(
    ['user_id' => $testUser->id, 'type' => 'bank'],
    ['bank_name' => 'Islami Bank', 'account_number' => '1234567890', 'account_name' => 'Concurrency User']
);

$wdr = WithdrawalRequest::create([
    'reference_id' => 'WDR-CONCUR-' . Str::random(6),
    'user_id' => $testUser->id,
    'withdrawal_method_id' => $method->id,
    'amount' => '300.00',
    'fee' => '0.00',
    'net_amount' => '300.00',
    'type' => 'bank',
    'status' => 'pending',
]);
// User balance debited on request: 1350 - 300 = 1050
$testUser->decrement('wallet_balance', 300.00);
$testUser->refresh();

$cmd1 = "\"$phpBinary\" \"$selfPath\" withdrawal {$wdr->id}";
$cmd2 = "\"$phpBinary\" \"$selfPath\" withdrawal {$wdr->id}";

$res3 = runParallel($cmd1, $cmd2);
$testUser->refresh();
$wdr->refresh();

$passed3 = ($testUser->wallet_balance == '1050.00' && $wdr->status === 'approved');
$results['withdrawal'] = [
    'withdrawal_id' => $wdr->id,
    'status' => $wdr->status,
    'user_final_balance' => (string)$testUser->wallet_balance,
    'expected_balance' => '1050.00', // Must remain 1050 (not deducted again, not refunded)
    'worker_outputs' => $res3,
    'passed' => $passed3,
];
echo "  -> Result: " . ($passed3 ? "PASSED (Balance: {$testUser->wallet_balance}, Status: {$wdr->status})" : "FAILED") . "\n";


// 4. Concurrent Escrow Release Test
echo "\n[TEST 4/4] Running concurrent escrow releases for identical deal...\n";
$deal = Deal::create([
    'deal_id' => 'DL-CONCUR-' . Str::random(6),
    'title' => 'Concurrency Escrow Deal',
    'initiator_id' => $testUser->id,
    'initiator_role' => 'buyer',
    'counterparty_id' => $testSeller->id,
    'counterparty_email' => $testSeller->email,
    'amount' => '500.00',
    'charge_total' => '25.00',
    'original_charge' => '25.00',
    'seller_gets' => '475.00',
    'buyer_pays' => '500.00',
    'status' => 'funds_secured',
    'admin_approved' => false,
]);

$cmd1 = "\"$phpBinary\" \"$selfPath\" deal {$deal->id}";
$cmd2 = "\"$phpBinary\" \"$selfPath\" deal {$deal->id}";

$res4 = runParallel($cmd1, $cmd2);
$testSeller->refresh();
$deal->refresh();

$dealTxnCount = Transaction::where('reference_type', 'App\\Models\\Deal')
    ->where('reference_id', $deal->id)
    ->count();

$passed4 = ($testSeller->wallet_balance == '475.00' && $deal->status === 'completed' && $dealTxnCount === 1);
$results['escrow'] = [
    'deal_id' => $deal->id,
    'status' => $deal->status,
    'admin_approved' => (bool)$deal->admin_approved,
    'seller_final_balance' => (string)$testSeller->wallet_balance,
    'expected_seller_balance' => '475.00', // Seller gets 475.00 EXACTLY ONCE (NOT 950.00)
    'transactions_created' => $dealTxnCount,
    'worker_outputs' => $res4,
    'passed' => $passed4,
];
echo "  -> Result: " . ($passed4 ? "PASSED (Seller Balance: {$testSeller->wallet_balance}, Status: {$deal->status}, Txns: 1)" : "FAILED") . "\n";

// Overall Assessment
$allPassed = $results['webhook']['passed'] && $results['deposit']['passed'] && $results['withdrawal']['passed'] && $results['escrow']['passed'];

echo "\n" . str_repeat('=', 60) . "\n";
echo "OVERALL CONCURRENCY VERIFICATION: " . ($allPassed ? "100% PASSED" : "FAILED") . "\n";
echo str_repeat('=', 60) . "\n";

file_put_contents(__DIR__ . '/../docs/phase-9/concurrency_test_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Results saved to docs/phase-9/concurrency_test_results.json\n";

// Cleanup test fixtures
$testUser->delete();
$testSeller->delete();
$dep->delete();
$wdr->delete();
$deal->delete();
PaymentWebhookIdempotency::where('gateway_slug', 'bkash')->where('payment_reference', $ref)->delete();
Transaction::where('description', 'like', "%{$ref}%")->delete();
Transaction::where('reference_type', 'App\\Models\\Deposit')->where('reference_id', $dep->id)->delete();
Transaction::where('reference_type', 'App\\Models\\Deal')->where('reference_id', $deal->id)->delete();
echo "Cleanup complete.\n";
