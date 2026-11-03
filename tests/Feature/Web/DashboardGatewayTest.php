<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_visiting_dashboard_is_redirected_to_auth(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/auth');
    }

    public function test_guest_visiting_dashboard_subpath_is_redirected_to_auth(): void
    {
        $response = $this->get('/dashboard/deals');
        $response->assertRedirect('/auth');
    }

    public function test_authenticated_customer_can_render_dashboard(): void
    {
        $customer = User::factory()->create([
            'username' => 'testcustomer',
            'name' => 'Test Customer',
            'role' => 'user',
            'wallet_balance' => 5000.00,
            'status' => 'Active',
            'verification_status' => 'VERIFIED',
        ]);

        $response = $this->actingAs($customer)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Customer Dashboard | MigratePay Escrow');
        $response->assertSee('id="app" data-portal="user"', false);
        $response->assertSee('"username":"testcustomer"', false);
        $response->assertSee('"wallet_balance":"5000.00"', false);
    }

    public function test_authenticated_customer_can_render_dashboard_subroutes(): void
    {
        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($customer)->get('/dashboard/deals');

        $response->assertStatus(200);
        $response->assertSee('Customer Dashboard | MigratePay Escrow');
        $response->assertSee('id="app" data-portal="user"', false);
    }

    public function test_authenticated_admin_visiting_dashboard_is_redirected_to_admin(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect('/admin');
    }

    public function test_guest_visiting_admin_is_redirected_to_auth_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/auth?role=admin');
    }

    public function test_customer_visiting_admin_is_redirected_to_auth_admin(): void
    {
        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($customer)->get('/admin');
        $response->assertRedirect('/auth?role=admin');
    }

    public function test_authenticated_admin_can_render_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'username' => 'leadadmin',
            'name' => 'Lead Administrator',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('MigratePay | Control Tower Admin');
        $response->assertSee('id="app" data-portal="admin"', false);
        $response->assertSee('"role":"admin"', false);
    }
}

