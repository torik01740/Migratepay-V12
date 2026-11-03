<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentGatewayController extends Controller
{
    public function __construct(
        protected PaymentGatewayService $gatewayService
    ) {}

    /**
     * Get public gateway settings for deposit UI.
     * Sanitized public endpoint replacing insecure gateway_settings.json.
     */
    public function publicSettings(): JsonResponse
    {
        $gateways = PaymentGateway::active()->get();

        if ($gateways->isEmpty()) {
            $defaultGateways = [
                [
                    'id'             => 1,
                    'name'           => 'Bank Transfer',
                    'slug'           => 'bank_transfer',
                    'icon'           => 'bank',
                    'currency'       => 'BDT',
                    'instructions'   => 'Deposit directly to our corporate bank account.',
                    'fee_percentage' => '0.00',
                    'fee_fixed'      => '0.00',
                    'is_active'      => true,
                ],
                [
                    'id'             => 2,
                    'name'           => 'bKash',
                    'slug'           => 'bkash',
                    'icon'           => 'bkash',
                    'currency'       => 'BDT',
                    'instructions'   => 'Send money to our verified bKash merchant number.',
                    'fee_percentage' => '1.50',
                    'fee_fixed'      => '0.00',
                    'is_active'      => true,
                ],
                [
                    'id'             => 3,
                    'name'           => 'Nagad',
                    'slug'           => 'nagad',
                    'icon'           => 'nagad',
                    'currency'       => 'BDT',
                    'instructions'   => 'Send money to our verified Nagad merchant number.',
                    'fee_percentage' => '1.50',
                    'fee_fixed'      => '0.00',
                    'is_active'      => true,
                ],
            ];

            return response()->json($defaultGateways);
        }

        $publicList = $gateways->map(fn (PaymentGateway $g) => $g->toPublicArray());

        return response()->json($publicList);
    }

    /**
     * Initiate online payment checkout session.
     */
    public function initiateCheckout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'gateway' => 'required|string',
            'amount'  => 'required|numeric|min:10',
            'purpose' => 'nullable|string|in:deposit,deal',
            'deal_id' => 'nullable|integer',
        ]);

        $result = $this->gatewayService->createCheckout(
            Auth::user(),
            $validated['gateway'],
            (string)$validated['amount'],
            $validated['purpose'] ?? 'deposit',
            $validated['deal_id'] ?? null
        );

        return response()->json([
            'success'  => true,
            'checkout' => $result,
        ]);
    }

    /**
     * Process incoming gateway webhook.
     */
    public function handleWebhook(Request $request, string $gateway): JsonResponse
    {
        $signature = $request->header('X-Signature') ?? $request->header('X-Webhook-Signature');
        $result = $this->gatewayService->processCallback($gateway, $request->all(), $signature);

        return response()->json($result);
    }

    /**
     * Process incoming browser callback (server verifies with gateway).
     */
    public function handleCallback(Request $request, string $gateway): JsonResponse
    {
        $result = $this->gatewayService->processCallback($gateway, $request->all());

        return response()->json($result);
    }

    /**
     * Admin list of payment gateways with masked credentials.
     */
    public function adminIndex(): JsonResponse
    {
        $gateways = PaymentGateway::all()->map(function (PaymentGateway $gateway) {
            $data = $gateway->toArray();
            $data['has_api_key'] = !empty($gateway->api_key);
            $data['has_secret_key'] = !empty($gateway->secret_key);
            $data['has_store_password'] = !empty($gateway->store_password);
            $data['api_key_masked'] = $gateway->api_key ? substr($gateway->api_key, 0, 4) . '••••••••' : null;
            $data['secret_key_masked'] = $gateway->secret_key ? substr($gateway->secret_key, 0, 4) . '••••••••' : null;
            return $data;
        });

        return response()->json([
            'success'  => true,
            'gateways' => $gateways,
        ]);
    }

    /**
     * Admin update payment gateway credentials and configuration.
     */
    public function adminUpdate(Request $request, PaymentGateway $gateway): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'sometimes|string|max:100',
            'instructions'   => 'nullable|string',
            'fee_percentage' => 'sometimes|numeric|min:0|max:100',
            'fee_fixed'      => 'sometimes|numeric|min:0',
            'api_key'        => 'nullable|string',
            'secret_key'     => 'nullable|string',
            'store_id'       => 'nullable|string',
            'store_password' => 'nullable|string',
            'is_active'      => 'sometimes|boolean',
        ]);

        foreach (['api_key', 'secret_key', 'store_password'] as $secret) {
            if (isset($validated[$secret]) && str_contains($validated[$secret], '••••••••')) {
                unset($validated[$secret]);
            }
        }

        $gateway->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Payment gateway '{$gateway->name}' configuration updated.",
            'gateway' => $gateway->fresh()->toPublicArray(),
        ]);
    }
}
