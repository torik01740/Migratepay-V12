<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatHistory;
use App\Models\AiSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAdminController extends Controller
{
    /**
     * Admin: Get AI configuration settings (masking secrets).
     */
    public function getSettings(): JsonResponse
    {
        $settings = AiSetting::all()->pluck('value', 'key')->toArray();

        // Mask sensitive API keys
        foreach (['api_key', 'openai_api_key', 'anthropic_api_key'] as $secretKey) {
            if (isset($settings[$secretKey]) && !empty($settings[$secretKey])) {
                $val = (string)$settings[$secretKey];
                $settings[$secretKey] = substr($val, 0, 4) . '••••••••' . substr($val, -4);
            }
        }

        return response()->json($settings);
    }

    /**
     * Admin: Save AI settings (encrypting sensitive values).
     */
    public function saveSettings(Request $request): JsonResponse
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            // If masked value submitted, don't overwrite
            if (is_string($value) && str_contains($value, '••••••••')) {
                continue;
            }

            AiSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Admin: Get chat history grouped by visitor session.
     */
    public function getHistory(): JsonResponse
    {
        $history = AiChatHistory::orderBy('created_at', 'asc')->get()->groupBy('session_id');

        $sessions = [];
        foreach ($history as $sessionId => $messages) {
            $firstUserMsg = $messages->firstWhere(function ($msg) {
                return !empty($msg->visitor_name);
            });

            $sessions[] = [
                'session_id'      => $sessionId,
                'visitor_name'    => $firstUserMsg ? $firstUserMsg->visitor_name : 'Visitor',
                'visitor_contact' => $firstUserMsg ? $firstUserMsg->visitor_contact : null,
                'created_at'      => $messages->last()->created_at,
                'message_count'   => $messages->count(),
                'messages'        => $messages,
            ];
        }

        usort($sessions, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        return response()->json($sessions);
    }
}
