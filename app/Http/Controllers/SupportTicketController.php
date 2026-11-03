<?php

namespace App\Http\Controllers;

use App\Services\SupportTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function __construct(
        protected SupportTicketService $ticketService
    ) {}

    /**
     * Get tickets formatted for frontend display.
     */
    public function index(Request $request): JsonResponse
    {
        $tickets = $this->ticketService->getFormattedTickets(
            Auth::user(),
            $request->query('source'),
            $request->query('guest_email')
        );

        return response()->json($tickets);
    }

    /**
     * Create a new support ticket.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'message'     => 'required|string',
            'priority'    => 'nullable|string|in:Low,Medium,High,Urgent,low,medium,high,urgent',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:10240',
            'source'      => 'nullable|string|max:50',
            'guest_name'  => 'nullable|string|max:100',
            'guest_email' => 'nullable|email|max:100',
        ]);

        if (isset($validated['priority'])) {
            $validated['priority'] = ucfirst(strtolower($validated['priority']));
        }

        $ticket = $this->ticketService->createTicket(
            Auth::user(),
            $validated,
            $request->file('attachment')
        );

        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully.',
            'ticket'  => $ticket,
        ], 201);
    }

    /**
     * Reply to a support ticket.
     */
    public function reply(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'message'     => 'required|string',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:10240',
            'guest_email' => 'nullable|email',
        ]);

        $message = $this->ticketService->replyTicket(
            Auth::user(),
            $id,
            $request->input('message'),
            $request->file('attachment'),
            $request->input('guest_email')
        );

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Admin: Close a support ticket.
     */
    public function close(string $id): JsonResponse
    {
        $ticket = $this->ticketService->closeTicket(Auth::user(), $id);
        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully.',
            'ticket'  => $ticket,
        ]);
    }

    /**
     * Sync local storage tickets to database.
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate(['tickets' => 'required|array']);
        $this->ticketService->syncLocalTickets(Auth::user(), $request->input('tickets'));

        return response()->json(['success' => true]);
    }
}
