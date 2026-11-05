<?php

namespace App\Http\Controllers;

use App\Services\WithdrawalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function __construct(
        protected WithdrawalService $withdrawalService
    ) {}

    /**
     * Get user's withdrawal methods.
     */
    public function methodsIndex(): JsonResponse
    {
        $methods = $this->withdrawalService->getUserMethods(Auth::id());
        return response()->json([
            'success' => true,
            'methods' => $methods,
        ]);
    }

    /**
     * Store or update a withdrawal method.
     */
    public function methodStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'            => 'required|in:bank,mobile',
            'bank_name'       => 'required_if:type,bank|nullable|string|max:100',
            'branch'          => 'required_if:type,bank|nullable|string|max:100',
            'routing_number'  => 'required_if:type,bank|nullable|string|max:50',
            'account_name'    => 'required_if:type,bank|nullable|string|max:100',
            'account_number'  => 'required_if:type,bank|nullable|string|max:50',
            'mobile_provider' => 'required_if:type,mobile|nullable|string|max:50',
            'mobile_number'   => 'required_if:type,mobile|nullable|string|max:50',
            'method_id'       => 'nullable|integer',
        ]);

        $result = $this->withdrawalService->storeMethod(Auth::user(), $validated, $request->input('method_id'));

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'method'  => $result['method'],
        ]);
    }

    /**
     * Delete a withdrawal method.
     */
    public function methodDestroy(int $id): JsonResponse
    {
        $this->withdrawalService->deleteMethod(Auth::user(), $id);
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal method deleted successfully.',
        ]);
    }

    /**
     * Get user's withdrawal requests.
     */
    public function index(): JsonResponse
    {
        $withdrawals = $this->withdrawalService->getUserWithdrawals(Auth::id());
        return response()->json([
            'success'     => true,
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
     * Request a withdrawal.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'withdrawal_method_id' => 'required|integer',
            'amount'               => 'required|numeric|min:50',
        ]);

        $result = $this->withdrawalService->requestWithdrawal(Auth::user(), $validated);

        return response()->json([
            'success'        => true,
            'message'        => 'Withdrawal request submitted successfully.',
            'withdrawal'     => $result['withdrawal'],
            'wallet_balance' => $result['wallet_balance'],
        ], 201);
    }

    /**
     * Admin: List all withdrawal requests with optional status filter.
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $withdrawals = $this->withdrawalService->getAdminWithdrawals($request->query('status'));
        return response()->json([
            'success'     => true,
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
     * Admin: Approve a withdrawal request.
     */
    public function adminApprove(Request $request, int $id): JsonResponse
    {
        $request->validate(['admin_note' => 'nullable|string|max:500']);
        try {
            $withdrawal = $this->withdrawalService->approveWithdrawal($id, Auth::user(), $request->input('admin_note'));

            return response()->json([
                'success'    => true,
                'message'    => 'Withdrawal approved successfully.',
                'withdrawal' => $withdrawal,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Admin: Reject a withdrawal request and refund wallet.
     */
    public function adminReject(Request $request, int $id): JsonResponse
    {
        $request->validate(['admin_note' => 'required|string|max:500']);
        try {
            $withdrawal = $this->withdrawalService->rejectWithdrawal($id, Auth::user(), $request->input('admin_note'));

            return response()->json([
                'success'    => true,
                'message'    => 'Withdrawal rejected and funds refunded to user.',
                'withdrawal' => $withdrawal,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
