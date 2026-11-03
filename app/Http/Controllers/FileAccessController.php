<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Deposit;
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileAccessController extends Controller
{
    /**
     * Stream authorized deposit receipt from private disk.
     */
    public function streamDepositReceipt(Request $request, Deposit $deposit): Response
    {
        $user = $request->user();
        if (!$user || (!$user->isAdmin() && $user->id !== $deposit->user_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized file access.'], 403);
        }

        if (empty($deposit->screenshot_path)) {
            return response()->json(['success' => false, 'message' => 'Receipt file not found.'], 404);
        }

        // Sanitize path against directory traversal
        $filename = basename($deposit->screenshot_path);
        $path = 'deposits/' . $filename;

        $disk = Storage::disk('private');
        if (!$disk->exists($path)) {
            if (Storage::disk('local')->exists($path)) {
                $disk = Storage::disk('local');
            } else {
                return response()->json(['success' => false, 'message' => 'Physical file not found.'], 404);
            }
        }

        return $disk->response($path, $filename, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream authorized support ticket attachment from private disk.
     */
    public function streamSupportAttachment(Request $request, SupportTicketMessage $message): Response
    {
        $user = $request->user();
        $ticket = $message->ticket;

        if (!$ticket || !$user || (!$user->isAdmin() && $user->id !== $ticket->user_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized file access.'], 403);
        }

        if (empty($message->attachment_path)) {
            return response()->json(['success' => false, 'message' => 'Attachment not found.'], 404);
        }

        $filename = basename($message->attachment_path);
        $path = 'support_attachments/' . $filename;

        $disk = Storage::disk('private');
        if (!$disk->exists($path)) {
            if (Storage::disk('local')->exists($path)) {
                $disk = Storage::disk('local');
            } else {
                return response()->json(['success' => false, 'message' => 'Physical file not found.'], 404);
            }
        }

        return $disk->response($path, $filename, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Stream authorized deal deliverables from private disk.
     */
    public function streamDealDeliverable(Request $request, Deal $deal, string $filename): Response
    {
        $user = $request->user();
        if (!$user || (!$user->isAdmin() && $user->id !== $deal->initiator_id && $user->id !== $deal->counterparty_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized deliverable access.'], 403);
        }

        $safeFilename = basename($filename);
        $path = 'deal_deliverables/' . $deal->id . '/' . $safeFilename;

        $disk = Storage::disk('private');
        if (!$disk->exists($path)) {
            // Also check under deals/{deal_id}/deliverables
            $altPath = "deals/{$deal->id}/deliverables/{$safeFilename}";
            if ($disk->exists($altPath)) {
                $path = $altPath;
            } else {
                return response()->json(['success' => false, 'message' => 'Deliverable file not found.'], 404);
            }
        }

        return $disk->response($path, $safeFilename, [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Unified streaming endpoint by type and identifier.
     */
    public function streamById(Request $request, string $type, int $id): Response
    {
        if ($type === 'deposit') {
            $deposit = Deposit::findOrFail($id);
            return $this->streamDepositReceipt($request, $deposit);
        }

        if ($type === 'support_attachment') {
            $message = SupportTicketMessage::findOrFail($id);
            return $this->streamSupportAttachment($request, $message);
        }

        return response()->json(['success' => false, 'message' => 'Invalid file stream request.'], 400);
    }
}
