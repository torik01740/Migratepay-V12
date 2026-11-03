<?php

namespace Tests\Feature\Web;

use App\Models\Category;
use App\Models\Deal;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully_with_branding_and_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MigratePay');
        $response->assertSee("Don't Just Trust the Deal.", false);
        $response->assertSee('Protect It.');
        $response->assertSee('How MigratePay Works');
        $response->assertSee('Calculate Your Deal Protection Fee');
        $response->assertSee('The 3 Guarantees');
    }

    public function test_core_marketing_pages_load_successfully(): void
    {
        $this->get('/about')->assertStatus(200)->assertSee('About Us');
        $this->get('/how-it-works')->assertRedirect('/#how-it-works');
        $this->get('/faq')->assertStatus(200)->assertSee('Frequently Asked Questions');
        $this->get('/contact')->assertStatus(200)->assertSee('Send Us a Message');
        $this->get('/privacy-policy')->assertStatus(200)->assertSee('PRIVACY POLICY');
        $this->get('/terms-and-conditions')->assertStatus(200)->assertSee('TERMS & CONDITIONS', false);
        $this->get('/cookies')->assertStatus(200)->assertSee('Cookie');
        $this->get('/our-teams')->assertStatus(200);
        $this->get('/documents')->assertStatus(200);
        $this->get('/blog')->assertStatus(200);
        $this->get('/gallery')->assertStatus(200);
        $this->get('/charge-calculator')->assertStatus(200)->assertSee('Charge Calculator');
        $this->get('/calculator')->assertRedirect('/charge-calculator');
    }

    public function test_auth_navigation_and_protected_gateways(): void
    {
        $this->get('/auth')->assertStatus(200)->assertSee('MigratePay');
        $this->get('/login')->assertRedirect('/auth');
        $this->get('/register')->assertRedirect('/auth?tab=register');
        $this->get('/dashboard')->assertRedirect('/auth');
        $this->get('/admin')->assertRedirect('/auth?role=admin');
    }

    public function test_fee_calculator_api_matches_phase_4_commission_tiers(): void
    {
        $category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'is_active' => true,
        ]);

        // Tier 4 (> 15,000 => 3%)
        $response1 = $this->getJson("/api/public/calculate-charge?amount=30000&category_id={$category->id}&charge_payer=shared_50");
        $response1->assertStatus(200);
        $response1->assertJsonPath('success', true);
        $response1->assertJsonPath('data.total_charge', '900.00');
        $response1->assertJsonPath('data.buyer_charge', '450.00');
        $response1->assertJsonPath('data.seller_charge', '450.00');

        // Tier 1 (1,000 - 5,000 => 5%)
        $response2 = $this->getJson("/api/public/calculate-charge?amount=5000&category_id={$category->id}&charge_payer=shared_50");
        $response2->assertStatus(200);
        $response2->assertJsonPath('data.total_charge', '250.00');
        $response2->assertJsonPath('data.buyer_charge', '125.00');
        $response2->assertJsonPath('data.seller_charge', '125.00');

        // Buyer 100%
        $response3 = $this->getJson("/api/public/calculate-charge?amount=10000&category_id={$category->id}&charge_payer=buyer_100");
        $response3->assertStatus(200);
        // Tier 2 (5,001 - 10,000 => 4.5% = 450)
        $response3->assertJsonPath('data.total_charge', '450.00');
        $response3->assertJsonPath('data.buyer_charge', '450.00');
        $response3->assertJsonPath('data.seller_charge', '0.00');
    }

    public function test_public_profiles_directory_and_privacy_safeguards(): void
    {
        $user = User::factory()->create([
            'name' => 'Private Counterparty',
            'username' => 'private_counterparty',
            'user_id_string' => 'USR-998877',
            'email' => 'supersecret@example.com',
            'phone' => '+8801999999999',
            'wallet_balance' => 99999.00,
            'nid_number' => 'NID-SECRET-999',
            'role' => 'user',
            'division' => 'Dhaka',
            'district' => 'Dhaka',
            'bio' => 'Professional Web Developer on MigratePay.',
        ]);

        $admin = User::factory()->create([
            'name' => 'System Administrator',
            'username' => 'sysadmin',
            'user_id_string' => 'ADM-001',
            'email' => 'admin@migratepay.com',
            'role' => 'admin',
        ]);

        // Profiles directory
        $response = $this->get('/profiles');
        $response->assertStatus(200);
        $response->assertSee('Private Counterparty');
        $response->assertDontSee('System Administrator');

        // Profile show page
        $showResponse = $this->get("/user/{$user->username}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Private Counterparty');
        $showResponse->assertSee('Professional Web Developer');

        // Strict Privacy Safeguards
        $showResponse->assertDontSee('supersecret@example.com');
        $showResponse->assertDontSee('+8801999999999');
        $showResponse->assertDontSee('99999.00');
        $showResponse->assertDontSee('NID-SECRET-999');

        // Admin profile must 404
        $adminShow = $this->get("/user/{$admin->username}");
        $adminShow->assertStatus(404);
    }

    public function test_contact_form_submits_honestly_to_support_ticket_service(): void
    {
        $response = $this->postJson('/api/support-tickets', [
            'guest_name' => 'Sakib Al Hasan',
            'guest_email' => 'sakib@example.com',
            'subject' => 'Escrow Inquiry',
            'message' => 'I would like to know how domain escrow works on MigratePay.',
            'priority' => 'medium',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('support_tickets', [
            'guest_name' => 'Sakib Al Hasan',
            'guest_email' => 'sakib@example.com',
            'subject' => 'Escrow Inquiry',
        ]);
    }
}
