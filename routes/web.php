<?php

use App\Http\Controllers\Admin\AiAdminController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\AiSupportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CmsBlogController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

// ── Public Marketing Pages ────────────────────────────────────────────────────
Route::get('/', function () {
    $activeBanner = null;
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('banners')) {
            $activeBanner = \App\Models\Banner::active()->first();
        }
    } catch (\Throwable $e) {
        $activeBanner = null;
    }
    return view('welcome', compact('activeBanner'));
})->name('home');

Route::get('/about', fn () => view('pages.about'))->name('about');
Route::get('/how-it-works', fn () => redirect('/#how-it-works'))->name('how-it-works');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');
Route::get('/privacy-policy', fn () => view('pages.privacy'))->name('privacy');
Route::get('/terms-and-conditions', fn () => view('pages.terms'))->name('terms');
Route::get('/cookies', fn () => view('pages.cookies'))->name('cookies');
Route::get('/faq', fn () => view('pages.faq'))->name('faq');
Route::get('/our-teams', fn () => view('pages.team'))->name('team');
Route::get('/documents', fn () => view('pages.documents'))->name('documents');
Route::get('/blog', fn () => view('pages.blog'))->name('blog');
Route::get('/gallery', fn () => view('pages.gallery'))->name('gallery');
Route::get('/charge-calculator', fn () => view('pages.calculator'))->name('calculator');
Route::get('/calculator', fn () => redirect('/charge-calculator'));

// Authentication Screen (Web)
Route::get('/auth', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->role === 'admin' ? '/admin' : '/dashboard');
    }
    return view('auth');
})->name('auth');

Route::get('/login', fn () => redirect('/auth'))->name('login');
Route::get('/register', fn () => redirect('/auth?tab=register'))->name('register');

// Protected Dashboard Gateways (Phase 6 React 19 + Chakra UI 3 Shell)
Route::get('/dashboard/{any?}', function () {
    if (!auth()->check()) {
        return redirect('/auth');
    }
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect('/admin');
    }
    return view('dashboard', ['authUser' => $user]);
})->where('any', '.*')->name('dashboard');

Route::get('/admin/{any?}', function () {
    if (!auth()->check() || auth()->user()->role !== 'admin') {
        return redirect('/auth?role=admin');
    }
    $user = auth()->user();
    return view('admin', ['authUser' => $user]);
})->where('any', '.*')->name('admin.dashboard');


// ─────────────────────────────────────────────────────────────────────────────
// 1. Authentication & Identity Management
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/api/csrf-token', fn () => response()->json(['success' => true, 'csrf_token' => csrf_token()]));
Route::post('/api/register', [AuthController::class, 'register']);
Route::post('/api/check-availability', [AuthController::class, 'checkAvailability']);
Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/logout', [AuthController::class, 'logout']);
Route::post('/api/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/api/reset-password', [AuthController::class, 'resetPassword']);

// ─────────────────────────────────────────────────────────────────────────────
// 2. Public API Endpoints (Sanitized, No Secrets Exposed)
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/api/public/categories', [CategoryController::class, 'index']);
Route::get('/api/categories', [CategoryController::class, 'index']);
Route::get('/api/public/gateway-settings', [PaymentGatewayController::class, 'publicSettings']);
Route::get('/api/payment-gateways/public', [PaymentGatewayController::class, 'publicSettings']); // Canonical Alias
Route::get('/api/public/calculate-charge', [DealController::class, 'calculateChargeAPI']);
Route::get('/api/public/banners', [BannerController::class, 'publicIndex']);
Route::get('/api/public/cms/{slug}', [CmsBlogController::class, 'publicCms']);
Route::get('/api/public/blog', [CmsBlogController::class, 'publicBlogIndex']);
Route::get('/api/public/blog/{slug}', [CmsBlogController::class, 'publicBlogShow']);

// Public Profiles
Route::get('/profiles', [PublicProfileController::class, 'index'])->name('profiles.index');
Route::get('/user/{identifier}', [PublicProfileController::class, 'show'])->name('profiles.show');

// AI Chatbot & Human Escalation (SEC-07: Nullable guest attribution)
Route::post('/api/ai/chat', [AiSupportController::class, 'chat']);
Route::post('/api/ai/escalate', [AiSupportController::class, 'escalate']);
Route::post('/api/support-tickets', [SupportTicketController::class, 'store']);

// Public / Customer-facing Counterparty Reviews
Route::get('/api/users/{user}/reviews', [UserController::class, 'reviews']);

// Payment Webhooks and Redirect Callbacks
Route::post('/api/payments/webhook/{gateway}', [PaymentGatewayController::class, 'handleWebhook']);
Route::get('/api/payments/callback/{gateway}', [PaymentGatewayController::class, 'handleCallback']);
Route::get('/payments/checkout/redirect/{ref}', function ($ref) {
    return response()->json(['success' => true, 'payment_reference' => $ref, 'status' => 'checkout_pending']);
})->name('payments.checkout.redirect');

// ─────────────────────────────────────────────────────────────────────────────
// 3. Authenticated Customer API Endpoints
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Current user sync & profile
    Route::get('/api/me', [AuthController::class, 'me']);
    Route::get('/api/user/profile', [AuthController::class, 'me']);
    Route::put('/api/profile', [ProfileController::class, 'update']);
    Route::match(['post', 'put'], '/api/user/profile', [ProfileController::class, 'update']);
    Route::post('/api/change-password', [AuthController::class, 'updatePassword']);
    Route::post('/api/user/change-password', [AuthController::class, 'updatePassword']);
    Route::post('/api/user/request-verification', [AuthController::class, 'requestVerification']);

    // Transactions History
    Route::get('/api/transactions', function () {
        $user = auth()->user();
        $transactions = $user->transactions()->latest()->paginate(25);
        return response()->json([
            'success' => true,
            'transactions' => $transactions->items(),
            'data' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    });

    // Deposits
    Route::get('/api/deposits', [DepositController::class, 'index']);
    Route::get('/api/deposit-methods', [PaymentGatewayController::class, 'publicSettings']);
    Route::post('/api/deposits', [DepositController::class, 'store']);
    Route::post('/api/deposits/upload-screenshot', [DepositController::class, 'uploadScreenshot']);

    // Online Payments
    Route::post('/api/payments/checkout', [PaymentGatewayController::class, 'initiateCheckout']);

    // Deals & Escrow Lifecycle
    Route::get('/api/deals', [DealController::class, 'index']);
    Route::post('/api/deals', [DealController::class, 'store']);
    Route::get('/api/deals/{id}', [DealController::class, 'show']);
    Route::post('/api/deals/{id}/accept', [DealController::class, 'accept']);
    Route::post('/api/deals/{id}/decline', [DealController::class, 'decline']);
    Route::post('/api/deals/{id}/release', [DealController::class, 'release']);
    Route::post('/api/deals/{id}/message', [DealController::class, 'sendMessage']);
    Route::post('/api/deals/{id}/submit', [DealController::class, 'submitWork']);
    Route::post('/api/deals/{id}/deliverable', [DealController::class, 'submitWork']);
    Route::post('/api/deals/{id}/review-submission', [DealController::class, 'reviewSubmission']);
    Route::post('/api/deals/{id}/extension', [DealController::class, 'requestExtension']);
    Route::post('/api/deals/{id}/approve-extension', [DealController::class, 'approveExtension']);
    Route::post('/api/deals/{id}/report', [DealController::class, 'reportIssue']);
    Route::get('/api/deals/{id}/reports', [DealController::class, 'getReports']);
    Route::post('/api/deals/{id}/custom-service', [DealController::class, 'requestCustomService']);

    // Deal Reviews
    Route::post('/api/deals/{id}/review', [ReviewController::class, 'store']);
    Route::get('/api/deals/{id}/can-review', [ReviewController::class, 'canReview']);

    // Notifications
    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications', [NotificationController::class, 'store']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Withdrawal Methods & Requests
    Route::get('/api/withdrawal-methods', [WithdrawalController::class, 'methodsIndex']);
    Route::post('/api/withdrawal-methods', [WithdrawalController::class, 'methodStore']);
    Route::delete('/api/withdrawal-methods/{id}', [WithdrawalController::class, 'methodDestroy']);
    Route::get('/api/withdrawals', [WithdrawalController::class, 'index']);
    Route::post('/api/withdrawals', [WithdrawalController::class, 'store']);

    // Support Tickets
    Route::get('/api/support-tickets', [SupportTicketController::class, 'index']);
    Route::get('/api/support/tickets', [SupportTicketController::class, 'index']);
    Route::post('/api/support/tickets', [SupportTicketController::class, 'store']);
    Route::post('/api/support-tickets/{id}/reply', [SupportTicketController::class, 'reply']);
    Route::post('/api/support/tickets/{id}/reply', [SupportTicketController::class, 'reply']);
    Route::post('/api/support-tickets/sync', [SupportTicketController::class, 'sync']);

    // Private File Access (Policy Gated)
    Route::get('/api/private/deposits/{deposit}', [FileAccessController::class, 'streamDepositReceipt']);
    Route::get('/api/private/support-attachments/{message}', [FileAccessController::class, 'streamSupportAttachment']);
    Route::get('/api/private/deals/{deal}/deliverables/{filename}', [FileAccessController::class, 'streamDealDeliverable'])->where('filename', '.*');
    Route::get('/api/private/stream/{type}/{id}', [FileAccessController::class, 'streamById'])->name('private.files.stream');
});

// ─────────────────────────────────────────────────────────────────────────────
// 4. Administrative Endpoints (Strictly Gated by AdminMiddleware)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    // Canonical Admin Routes (/api/admin/*)
    Route::prefix('api/admin')->group(function () {
        // Users & Verification
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{user}/status', [UserController::class, 'updateStatus']);
        Route::put('/users/{user}/verification', [UserController::class, 'updateVerification']);

        // Deposits
        Route::get('/deposits', [DepositController::class, 'adminIndex']);
        Route::post('/deposits/{id}/approve', [DepositController::class, 'approve']);
        Route::post('/deposits/{id}/reject', [DepositController::class, 'reject']);
        Route::post('/deposits/{id}/read', [DepositController::class, 'markAsRead']);

        // Deals & Disputes
        Route::get('/deals', [DealController::class, 'adminIndex']);
        Route::post('/deals/{id}/approve-funds', [DealController::class, 'adminApproveFunds']);
        Route::get('/reports', [DealController::class, 'adminReports']);
        Route::post('/reports/{id}/resolve', [DealController::class, 'adminResolveReport']);

        // Withdrawals
        Route::get('/withdrawals', [WithdrawalController::class, 'adminIndex']);
        Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'adminApprove']);
        Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'adminReject']);

        // Coupons
        Route::get('/coupons', [CouponController::class, 'index']);
        Route::post('/coupons', [CouponController::class, 'store']);
        Route::get('/coupons/{id}', [CouponController::class, 'show']);
        Route::put('/coupons/{id}', [CouponController::class, 'update']);
        Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'adminIndex']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

        // Banners
        Route::get('/banners', [BannerController::class, 'index']);
        Route::post('/banners', [BannerController::class, 'store']);
        Route::get('/banners/{banner}', [BannerController::class, 'show']);
        Route::put('/banners/{banner}', [BannerController::class, 'update']);
        Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);

        // Support Tickets
        Route::post('/support-tickets/{id}/reply', [SupportTicketController::class, 'reply']);
        Route::post('/support-tickets/{id}/close', [SupportTicketController::class, 'close']);

        // AI Settings, Chat History & Knowledge Base
        Route::get('/ai-settings', [AiAdminController::class, 'getSettings']);
        Route::post('/ai-settings', [AiAdminController::class, 'saveSettings']);
        Route::get('/ai-chat-history', [AiAdminController::class, 'getHistory']);
        Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index']);
        Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store']);
        Route::put('/knowledge-base/{id}', [KnowledgeBaseController::class, 'update']);
        Route::delete('/knowledge-base/{id}', [KnowledgeBaseController::class, 'destroy']);

        // Analytics & Master Ledger
        Route::get('/reports/ledger', [AnalyticsController::class, 'getLedger']);
        Route::get('/analytics/dashboard', [AnalyticsController::class, 'getDashboardMetrics']);

        // Gateways
        Route::get('/gateway-settings', [PaymentGatewayController::class, 'adminIndex']);
        Route::post('/gateway-settings/{gateway}', [PaymentGatewayController::class, 'adminUpdate']);

        // CMS & Blog Management
        Route::get('/cms', [CmsBlogController::class, 'adminCmsIndex']);
        Route::post('/cms', [CmsBlogController::class, 'adminCmsStore']);
        Route::put('/cms/{id}', [CmsBlogController::class, 'adminCmsUpdate']);
        Route::delete('/cms/{id}', [CmsBlogController::class, 'adminCmsDestroy']);
        Route::get('/blog', [CmsBlogController::class, 'adminBlogIndex']);
        Route::post('/blog', [CmsBlogController::class, 'adminBlogStore']);
        Route::put('/blog/{id}', [CmsBlogController::class, 'adminBlogUpdate']);
        Route::delete('/blog/{id}', [CmsBlogController::class, 'adminBlogDestroy']);
    });

    // Categories Admin Management
    Route::get('/api/admin/categories', [CategoryController::class, 'index']);
    Route::post('/api/categories', [CategoryController::class, 'store']);
    Route::post('/api/admin/categories', [CategoryController::class, 'store']);
    Route::put('/api/categories/{id}', [CategoryController::class, 'update']);
    Route::put('/api/admin/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/api/categories/{id}', [CategoryController::class, 'destroy']);
    Route::delete('/api/admin/categories/{id}', [CategoryController::class, 'destroy']);

    // Backward-Compatible Aliases for existing adminApi.js
    Route::get('/api/users', [UserController::class, 'index']);
    Route::put('/api/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::put('/api/users/{user}/verification', [UserController::class, 'updateVerification']);
});
