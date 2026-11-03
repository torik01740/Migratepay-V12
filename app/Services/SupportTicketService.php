<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupportTicketService
{
    /**
     * Get tickets formatted for frontend display.
     */
    public function getFormattedTickets(?User $user, ?string $source = null, ?string $guestEmail = null): array
    {
        $query = SupportTicket::with(['messages.sender', 'user']);

        if ($user && $user->role === 'user') {
            $query->where('user_id', $user->id);
        } elseif (!$user && $guestEmail) {
            $query->whereNull('user_id')->where('guest_email', strtolower(trim($guestEmail)));
        }

        if ($source) {
            $query->where('source', $source);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return $tickets->map(function ($t) {
            $replies = $t->messages->map(function ($m) {
                return [
                    'id'         => $m->id,
                    'msg'        => $m->message,
                    'attachment' => $m->attachment_path ? route('private.files.stream', ['type' => 'support_attachment', 'id' => $m->id]) : null,
                    'at'         => $m->created_at ? $m->created_at->toISOString() : null,
                    'from'       => $m->sender_type,
                    'sender'     => $m->sender ? $m->sender->name : ($m->sender_type === 'Guest' ? 'Guest Visitor' : 'Support Agent'),
                ];
            });

            return [
                'id'         => str_replace('TKT-', '', $t->ticket_number),
                'ticket_num' => $t->ticket_number,
                'subject'    => $t->subject,
                'status'     => $t->status,
                'priority'   => $t->priority,
                'guestName'  => $t->guest_name,
                'guestEmail' => $t->guest_email,
                'createdAt'  => $t->created_at ? $t->created_at->toISOString() : null,
                'userEmail'  => $t->user ? $t->user->email : $t->guest_email,
                'replies'    => $replies,
            ];
        })->toArray();
    }

    /**
     * Create a new support ticket (for authenticated user or guest without user hijacking).
     */
    public function createTicket(?User $user, array $data, $attachment = null): SupportTicket
    {
        $attachmentPath = null;
        if ($attachment) {
            $attachmentPath = $attachment->store('support_attachments', 'private');
        }

        return DB::transaction(function () use ($user, $data, $attachmentPath) {
            $ticketNum = 'TKT-' . rand(100000, 999999);

            $ticket = SupportTicket::create([
                'user_id'       => $user?->id,
                'guest_name'    => $user ? null : ($data['guest_name'] ?? 'Guest Visitor'),
                'guest_email'   => $user ? null : strtolower(trim($data['guest_email'] ?? '')),
                'ticket_number' => $ticketNum,
                'subject'       => $data['subject'],
                'priority'      => $data['priority'] ?? 'Medium',
                'status'        => 'Open',
                'source'        => $data['source'] ?? 'Web',
            ]);

            SupportTicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id'         => $user?->id,
                'sender_type'       => $user ? ($user->role === 'admin' ? 'Admin' : 'User') : 'Guest',
                'message'           => $data['message'],
                'attachment_path'   => $attachmentPath,
            ]);

            return $ticket->load(['messages', 'user']);
        });
    }

    /**
     * Reply to a ticket.
     */
    public function replyTicket(?User $user, string $ticketId, string $message, $attachment = null, ?string $guestEmail = null): SupportTicketMessage
    {
        $ticketNum = 'TKT-' . str_replace('TKT-', '', $ticketId);
        $ticket = SupportTicket::where('ticket_number', $ticketNum)->first();

        if (!$ticket) {
            throw new RuntimeException('Ticket not found.');
        }

        // Authorization check
        if ($user) {
            if ($user->role === 'user' && $ticket->user_id !== $user->id) {
                throw new RuntimeException('Unauthorized to reply to this ticket.');
            }
            $senderType = $user->role === 'admin' ? 'Admin' : 'User';
        } else {
            // Guest verification
            if (!$guestEmail || strtolower(trim($guestEmail)) !== strtolower(trim($ticket->guest_email ?? ''))) {
                throw new RuntimeException('Unauthorized guest access to this ticket.');
            }
            $senderType = 'Guest';
        }

        if ($ticket->status === 'Closed') {
            throw new RuntimeException('Ticket is closed.');
        }

        $attachmentPath = null;
        if ($attachment) {
            $attachmentPath = $attachment->store('support_attachments', 'private');
        }

        return DB::transaction(function () use ($ticket, $user, $senderType, $message, $attachmentPath) {
            $ticket->status = $senderType === 'Admin' ? 'Replied' : 'Open';
            $ticket->save();

            return SupportTicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id'         => $user?->id,
                'sender_type'       => $senderType,
                'message'           => $message,
                'attachment_path'   => $attachmentPath,
            ]);
        });
    }

    /**
     * Admin: Close a ticket.
     */
    public function closeTicket(User $admin, string $ticketId): SupportTicket
    {
        $ticketNum = 'TKT-' . str_replace('TKT-', '', $ticketId);
        $ticket = SupportTicket::where('ticket_number', $ticketNum)->first();

        if (!$ticket) {
            throw new RuntimeException('Ticket not found.');
        }

        $ticket->status = 'Closed';
        $ticket->closed_at = now();
        $ticket->closed_by = $admin->id;
        $ticket->save();

        return $ticket;
    }

    /**
     * Sync local storage tickets to database for authenticated user.
     */
    public function syncLocalTickets(User $user, array $localTickets): void
    {
        DB::transaction(function () use ($user, $localTickets) {
            foreach ($localTickets as $lt) {
                $ticketNum = 'TKT-' . str_replace('TKT-', '', $lt['id']);
                $existing = SupportTicket::where('ticket_number', $ticketNum)->first();

                if (!$existing) {
                    $t = SupportTicket::create([
                        'user_id'       => $user->id,
                        'ticket_number' => $ticketNum,
                        'subject'       => $lt['subject'] ?? 'No Subject',
                        'status'        => $lt['status'] ?? 'Open',
                        'priority'      => $lt['priority'] ?? 'Medium',
                        'created_at'    => $lt['createdAt'] ?? now(),
                    ]);

                    if (!empty($lt['message'])) {
                        SupportTicketMessage::create([
                            'support_ticket_id' => $t->id,
                            'sender_id'         => $user->id,
                            'sender_type'       => 'User',
                            'message'           => $lt['message'],
                            'created_at'        => $lt['createdAt'] ?? now(),
                        ]);
                    }

                    if (isset($lt['replies']) && is_array($lt['replies'])) {
                        foreach ($lt['replies'] as $reply) {
                            $from = $reply['from'] ?? 'User';
                            SupportTicketMessage::create([
                                'support_ticket_id' => $t->id,
                                'sender_id'         => $from === 'User' ? $user->id : null,
                                'sender_type'       => $from,
                                'message'           => $reply['msg'],
                                'created_at'        => $reply['at'] ?? now(),
                            ]);
                        }
                    }
                }
            }
        });
    }
}
