<?php

namespace App\Http\Controllers;

use App\Models\AiChatHistory;
use App\Models\AiKnowledgeBase;
use App\Models\AiSetting;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiSupportController extends Controller
{
    /**
     * Handle incoming chat from the frontend AI widget.
     * Matches user message against knowledge base.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'required|string|max:255',
        ]);

        $userMessage = strtolower(trim($request->message));
        $sessionId = $request->session_id;

        // Log visitor message
        AiChatHistory::create([
            'session_id' => $sessionId,
            'visitor_name' => $request->input('visitor_name', ''),
            'visitor_contact' => $request->input('visitor_contact', ''),
            'sender_type' => 'user',
            'message' => $request->message,
        ]);

        // Default system responses
        $systemKbs = [
            [
                'keywords' => ['hello', 'hi', 'hey', 'greetings', 'help', 'salam'],
                'answer' => "Hello! How can I assist you with MigratePay today? I can explain how our secure escrow works, our platform fees, or help you understand the transaction process.",
            ],
            [
                'keywords' => ['escrow', 'how does it work', 'secure payment', 'safe'],
                'answer' => "MigratePay is a secure escrow platform. We hold buyer funds in trust until delivery milestones are fulfilled and approved, protecting both buyers and sellers.",
            ],
            [
                'keywords' => ['fee', 'charge', 'cost', 'percentage'],
                'answer' => "MigratePay charges a small platform fee for securing the transaction in escrow. You can review the exact fees prior to accepting a deal.",
            ],
        ];

        // Search active knowledge base
        $kbs = AiKnowledgeBase::where('status', 'Active')->get();
        $bestAnswer = null;

        foreach ($kbs as $kb) {
            $keywords = array_map('trim', explode(',', strtolower((string)$kb->keywords)));
            foreach ($keywords as $kw) {
                if (!empty($kw) && str_contains($userMessage, $kw)) {
                    $bestAnswer = $kb->answer;
                    break 2;
                }
            }
        }

        if (!$bestAnswer) {
            foreach ($systemKbs as $sys) {
                foreach ($sys['keywords'] as $kw) {
                    if (str_contains($userMessage, $kw)) {
                        $bestAnswer = $sys['answer'];
                        break 2;
                    }
                }
            }
        }

        if (!$bestAnswer) {
            $bestAnswer = "I'm not quite sure about that. Would you like to escalate this question to our human support team?";
            $escalationRequired = true;
        } else {
            $escalationRequired = false;
        }

        // Log AI response
        AiChatHistory::create([
            'session_id' => $sessionId,
            'sender_type' => 'ai',
            'message' => $bestAnswer,
        ]);

        return response()->json([
            'success' => true,
            'escalation_required' => $escalationRequired,
            'message' => $bestAnswer,
        ]);
    }

    /**
     * Escalate an unresolved AI chat to the human Support Ticket system.
     * SEC-07 Remediation: Guest tickets use user_id = null instead of hijacking User 1.
     */
    public function escalate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'question' => 'required|string|max:2000',
        ]);

        $user = auth()->user();
        $userId = $user ? $user->id : null;

        // Generate ticket number: TKT-XXXXXX
        $ticketNumber = 'TKT-' . strtoupper(Str::random(6));

        // Create ticket with proper guest attribution
        $ticket = SupportTicket::create([
            'user_id' => $userId, // NULL for guests (NEVER hardcoded fallback to 1)
            'guest_name' => $userId ? null : $request->name,
            'guest_email' => $userId ? null : $request->email,
            'ticket_number' => $ticketNumber,
            'subject' => 'AI Escalation: ' . Str::limit($request->question, 50),
            'priority' => 'General Inquiry',
            'status' => 'Open',
            'source' => 'AI_ESCALATION',
        ]);

        // Create first message with context
        $context = "Name: {$request->name}\nEmail: {$request->email}\n\nOriginal Question:\n{$request->question}";

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_id' => $userId,
            'sender_type' => $userId ? 'User' : 'Guest',
            'message' => $context,
            'is_read_by_user' => true,
            'is_read_by_admin' => false,
        ]);

        return response()->json([
            'success' => true,
            'ticket_number' => $ticket->ticket_number,
            'message' => "Your request has been escalated to the MigratePay support team. Reference: #{$ticket->ticket_number}",
        ]);
    }
}
