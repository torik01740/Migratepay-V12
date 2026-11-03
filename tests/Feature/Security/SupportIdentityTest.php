<?php

namespace Tests\Feature\Security;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_ai_escalation_does_not_hijack_user_1(): void
    {
        // Seed User 1 (John Doe)
        $johnDoe = User::factory()->create([
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'johndoe@email.com',
        ]);

        $response = $this->postJson('/api/ai/escalate', [
            'name' => 'Visitor Alice',
            'email' => 'alice@visitor.com',
            'question' => 'How do I fund my escrow deal safely?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'ticket_number']);

        $ticket = SupportTicket::latest()->first();
        $this->assertNotNull($ticket);

        // SEC-07 Invariant: Ticket must NOT belong to User 1
        $this->assertNull($ticket->user_id, 'Guest ticket user_id must be null.');
        $this->assertNotEquals(1, $ticket->user_id);
        $this->assertEquals('Visitor Alice', $ticket->guest_name);
        $this->assertEquals('alice@visitor.com', $ticket->guest_email);

        // John Doe's tickets count must remain 0
        $this->assertEquals(0, $johnDoe->supportTickets()->count());
    }

    public function test_authenticated_customer_escalation_attaches_to_own_account(): void
    {
        $customer = User::factory()->create([
            'name' => 'Pulok Halder',
            'email' => 'pulok@gmail.com',
        ]);

        $response = $this->actingAs($customer)->postJson('/api/ai/escalate', [
            'name' => 'Pulok Halder',
            'email' => 'pulok@gmail.com',
            'question' => 'When will my seller funds be released?',
        ]);

        $response->assertStatus(200);

        $ticket = SupportTicket::latest()->first();
        $this->assertEquals($customer->id, $ticket->user_id);
        $this->assertNull($ticket->guest_name);
    }
}
