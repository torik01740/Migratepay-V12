<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteBarrierTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->getJson('/api/admin/users');
        $response->assertStatus(401);

        $response2 = $this->getJson('/api/users');
        $response2->assertStatus(401);
    }

    public function test_customer_cannot_access_admin_routes(): void
    {
        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($customer)->getJson('/api/admin/users');
        $response->assertStatus(403);

        $response2 = $this->actingAs($customer)->getJson('/api/users');
        $response2->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->getJson('/api/admin/users');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'users']);

        $response2 = $this->actingAs($admin)->getJson('/api/users');
        $response2->assertStatus(200);
    }

    public function test_customer_can_access_user_reviews(): void
    {
        $targetUser = User::factory()->create([
            'username' => 'pulokhalder',
        ]);

        // Public visitor / customer review check (userAndSupport.js compatibility)
        $response = $this->getJson("/api/users/{$targetUser->id}/reviews");
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'user', 'reviews']);
    }
}
