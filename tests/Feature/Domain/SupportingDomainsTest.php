<?php

namespace Tests\Feature\Domain;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\DealService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportingDomainsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_crud_operations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create
        $response = $this->actingAs($admin)->postJson('/api/categories', [
            'name'        => 'Web Development',
            'description' => 'Websites and apps',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Web Development']);

        $category = Category::where('name', 'Web Development')->first();

        // Update
        $updateResp = $this->actingAs($admin)->putJson("/api/categories/{$category->id}", [
            'name'        => 'Full Stack Development',
            'description' => 'Updated description',
        ]);
        $updateResp->assertStatus(200);
        $this->assertEquals('Full Stack Development', $category->fresh()->name);

        // Delete
        $delResp = $this->actingAs($admin)->deleteJson("/api/categories/{$category->id}");
        $delResp->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_coupon_discount_calculation_and_usage_limits(): void
    {
        $user = User::factory()->create(['wallet_balance' => '5000.00']);
        $dealService = app(DealService::class);

        // Percentage coupon: 50% discount on charge
        $coupon = Coupon::create([
            'code'           => 'HALFCHARGE',
            'discount_type'  => 'percentage',
            'discount_value' => '50.00',
            'is_active'      => true,
            'usage_per_user' => 1,
        ]);

        // Amount: 1000. Charge @ 5% = 50. 50% discount = 25. Final charge = 25.
        $calc = $dealService->calculateCharge('1000.00', 'shared_50', 'HALFCHARGE', $user);
        $this->assertEquals('25.00', $calc['coupon_discount']);
        $this->assertEquals('25.00', $calc['total_charge']);
        $this->assertTrue($calc['coupon_applied']);
    }

    public function test_review_submission_enforces_deal_completion_and_party_membership(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $unrelated = User::factory()->create();

        $deal = Deal::create([
            'deal_id'            => 'DX-112233',
            'title'              => 'SEO Optimization',
            'amount'             => '1000.00',
            'buyer_pays'         => '1025.00',
            'seller_gets'        => '975.00',
            'charge_total'       => '50.00',
            'initiator_id'       => $buyer->id,
            'counterparty_id'    => $seller->id,
            'counterparty_email' => $seller->email,
            'initiator_role'     => 'buyer',
            'status'             => 'funds_secured', // Not completed yet
        ]);

        // Attempt review before deal completion -> must fail
        $response = $this->actingAs($buyer)->postJson("/api/deals/{$deal->id}/review", [
            'rating'  => 5,
            'comment' => 'Great work!',
        ]);
        $response->assertStatus(422);

        // Mark deal completed
        $deal->update(['status' => 'completed']);

        // Unrelated user cannot review -> 403
        $unrelatedResp = $this->actingAs($unrelated)->postJson("/api/deals/{$deal->id}/review", [
            'rating'  => 5,
            'comment' => 'Great work!',
        ]);
        $unrelatedResp->assertStatus(403);

        // Buyer reviews seller -> Success
        $successResp = $this->actingAs($buyer)->postJson("/api/deals/{$deal->id}/review", [
            'rating'        => 5,
            'communication' => 5,
            'behavior'      => 4,
            'delivery'      => 5,
            'reliability'   => 5,
            'comment'       => 'Excellent freelancer.',
        ]);
        $successResp->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'deal_id'     => $deal->id,
            'reviewer_id' => $buyer->id,
            'reviewee_id' => $seller->id,
            'role'        => 'buyer',
        ]);
    }

    public function test_support_ticket_creation_for_authenticated_and_guest(): void
    {
        $user = User::factory()->create(['name' => 'Alice Customer']);

        // Authenticated customer ticket
        $authResp = $this->actingAs($user)->postJson('/api/support-tickets', [
            'subject'  => 'Billing Question',
            'message'  => 'Need copy of invoice.',
            'priority' => 'Low',
        ]);
        $authResp->assertStatus(201);
        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $user->id,
            'subject' => 'Billing Question',
        ]);

        // Guest visitor ticket (without hijacking existing accounts)
        auth()->logout();
        $guestResp = $this->postJson('/api/support-tickets', [
            'subject'     => 'General Inquiry',
            'message'     => 'How does escrow work?',
            'guest_name'  => 'Bob Visitor',
            'guest_email' => 'bob.visitor@example.com',
        ]);
        $guestResp->assertStatus(201);
        $this->assertDatabaseHas('support_tickets', [
            'user_id'     => null,
            'guest_name'  => 'Bob Visitor',
            'guest_email' => 'bob.visitor@example.com',
            'subject'     => 'General Inquiry',
        ]);
    }

    public function test_public_profiles_endpoint_excludes_administrators(): void
    {
        User::factory()->create(['name' => 'Public Customer', 'role' => 'user']);
        User::factory()->create(['name' => 'Super Admin', 'role' => 'admin']);

        $response = $this->getJson('/profiles');
        $response->assertStatus(200);

        $names = collect($response->json('profiles.data'))->pluck('name');
        $this->assertTrue($names->contains('Public Customer'));
        $this->assertFalse($names->contains('Super Admin'));
    }
}
