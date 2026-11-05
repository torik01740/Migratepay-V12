<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Deposit;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Services\AuditLogService;
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
        if (!$user || (!$user->isStaff() && $user->id !== $deposit->user_id)) {
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

        if (!$ticket || !$user || (!$user->isStaff() && $user->id !== $ticket->user_id)) {
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
        if (!$user || (!$user->isStaff() && $user->id !== $deal->initiator_id && $user->id !== $deal->counterparty_id)) {
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
     * Stream authorized KYC identity verification document from private disk.
     * Enforces strict least-privilege: Document owner, root Admin, or Co-Admin only.
     * Editorial and Support roles are explicitly forbidden (HTTP 403).
     */
    public function streamKycDocument(Request $request, User $user): Response
    {
        $authUser = $request->user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isOwner = ($authUser->id === $user->id);
        $isComplianceStaff = ($authUser->isAdmin() || $authUser->hasRole('co-admin', 'co_admin'));

        if (!$isOwner && !$isComplianceStaff) {
            AuditLogService::log(
                'kyc.unauthorized_access_attempt',
                "User {$authUser->id} ({$authUser->role}) denied access to KYC document of user {$user->id}",
                ['actor_id' => $authUser->id, 'role' => $authUser->role, 'target_user_id' => $user->id],
                'warning'
            );
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized KYC document access. Only the document owner or compliance administrators are permitted.',
            ], 403);
        }

        if ($isComplianceStaff && !$isOwner) {
            AuditLogService::log(
                'kyc.document_viewed',
                "Staff {$authUser->email} ({$authUser->role}) viewed KYC document for user {$user->id}",
                ['staff_id' => $authUser->id, 'target_user_id' => $user->id]
            );
        }

        if (empty($user->kyc_document_path)) {
            return response()->json(['success' => false, 'message' => 'KYC document not found.'], 404);
        }

        // Sanitize path against directory traversal
        $filename = basename($user->kyc_document_path);
        $path = "kyc/{$user->id}/" . $filename;
        $rawPath = (str_contains($user->kyc_document_path, '..') || str_starts_with($user->kyc_document_path, '/'))
            ? null
            : $user->kyc_document_path;

        try {
            $disk = Storage::disk('private');
            if ($disk->exists($path)) {
                $chosenDisk = $disk;
                $chosenPath = $path;
            } elseif ($rawPath && $disk->exists($rawPath)) {
                $chosenDisk = $disk;
                $chosenPath = $rawPath;
            } elseif (Storage::disk('local')->exists($path)) {
                $chosenDisk = Storage::disk('local');
                $chosenPath = $path;
            } elseif ($rawPath && Storage::disk('local')->exists($rawPath)) {
                $chosenDisk = Storage::disk('local');
                $chosenPath = $rawPath;
            } else {
                return response()->json(['success' => false, 'message' => 'Physical document file not found.'], 404);
            }
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Physical document file not found.'], 404);
        }

        return $chosenDisk->response($chosenPath, $filename, [
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

        if ($type === 'kyc') {
            $targetUser = User::findOrFail($id);
            return $this->streamKycDocument($request, $targetUser);
        }

        return response()->json(['success' => false, 'message' => 'Invalid file stream request.'], 400);
    }
}
