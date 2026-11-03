<?php

namespace Tests\Feature\Security;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivateFileAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_cannot_stream_deposit_receipt(): void
    {
        $user = User::factory()->create();

        $deposit = Deposit::create([
            'user_id' => $user->id,
            'amount' => '1000.00',
            'gateway' => 'bank_transfer',
            'screenshot_path' => 'deposits/test_receipt.jpg',
            'status' => 'approved',
        ]);

        $response = $this->getJson("/api/private/deposits/{$deposit->id}");
        $response->assertStatus(401);
    }

    public function test_other_user_cannot_stream_deposit_receipt(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create(['role' => 'user']);

        $deposit = Deposit::create([
            'user_id' => $owner->id,
            'amount' => '1000.00',
            'gateway' => 'bank_transfer',
            'screenshot_path' => 'deposits/test_receipt.jpg',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($otherUser)->getJson("/api/private/deposits/{$deposit->id}");
        $response->assertStatus(403);
    }

    public function test_deposit_owner_and_admin_can_stream_receipt(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('deposits/test_receipt.jpg', 'fake-image-binary-data');

        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $deposit = Deposit::create([
            'user_id' => $owner->id,
            'amount' => '1000.00',
            'gateway' => 'bank_transfer',
            'screenshot_path' => 'deposits/test_receipt.jpg',
            'status' => 'approved',
        ]);

        // Owner streams receipt
        $ownerResponse = $this->actingAs($owner)->get("/api/private/deposits/{$deposit->id}");
        $ownerResponse->assertStatus(200);
        $ownerResponse->assertHeader('Pragma', 'no-cache');

        // Admin streams receipt
        $adminResponse = $this->actingAs($admin)->get("/api/private/deposits/{$deposit->id}");
        $adminResponse->assertStatus(200);
    }
}
