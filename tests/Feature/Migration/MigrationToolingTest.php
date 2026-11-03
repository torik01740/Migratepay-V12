<?php

namespace Tests\Feature\Migration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MigrationToolingTest extends TestCase
{
    use RefreshDatabase;

    public function test_dry_run_produces_zero_destination_writes(): void
    {
        $initialUserCount = User::count();

        $exitCode = Artisan::call('app:migrate-v10-data', ['--dry-run' => true]);
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('ZERO DESTINATION WRITES EXECUTED', $output);
        $this->assertStringContainsString('EXACT ZERO VARIANCE', $output);
        $this->assertStringContainsString('MATCH', $output);

        // Assert zero records were written to database
        $this->assertEquals($initialUserCount, User::count());
    }

    public function test_force_flag_is_strictly_blocked_without_authorization(): void
    {
        $exitCode = Artisan::call('app:migrate-v10-data', ['--force' => true]);
        $output = Artisan::output();

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('BLOCKED: Actual historical data import requires separate explicit written authorization', $output);
        $this->assertStringContainsString('Zero records were written', $output);
    }
}
