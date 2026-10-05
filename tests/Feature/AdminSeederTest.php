<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_admin_is_renamed_to_admin_email_keeping_password(): void
    {
        $old = User::factory()->create(['email' => 'old-admin@example.com', 'is_admin' => true, 'password' => 'old-secret-1']);
        $newer = User::factory()->create(['email' => 'second-admin@example.com', 'is_admin' => true]);
        User::factory()->create(['email' => 'user@example.com']);
        config(['site.admin_email' => 'admin@ystqez.com', 'site.admin_password' => 'new-password-2']);

        $this->seed(DatabaseSeeder::class);

        $old->refresh();
        $this->assertSame('admin@ystqez.com', $old->email);
        $this->assertTrue(Hash::check('old-secret-1', $old->password), 'password must stay unchanged');
        $this->assertSame('second-admin@example.com', $newer->fresh()->email);
        $this->assertSame(3, User::count());

        $this->seed(DatabaseSeeder::class); // idempotent
        $this->assertSame(1, User::where('email', 'admin@ystqez.com')->count());
        $this->assertSame(3, User::count());
    }

    public function test_admin_is_created_when_no_admin_exists(): void
    {
        config(['site.admin_email' => 'admin@ystqez.com', 'site.admin_password' => 'new-password-2']);
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@ystqez.com')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('new-password-2', $admin->password));
    }
}
