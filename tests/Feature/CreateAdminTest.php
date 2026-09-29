<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_an_admin_with_a_hashed_generated_password(): void
    {
        $this->assertSame(0, Artisan::call('sentinel:admin-create', ['email' => 'ADMIN@example.com']));
        preg_match('/Mot de passe : (\S+)/u', Artisan::output(), $matches);
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame(UserRole::ADMINISTRATEUR, $user->role);
        $this->assertSame('actif', $user->statut);
        $this->assertSame(20, strlen($matches[1]));
        $this->assertTrue(Hash::check($matches[1], $user->password));
    }

    public function test_existing_account_is_not_promoted_or_reset(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);
        $passwordHash = $user->password;
        $this->artisan('sentinel:admin-create existing@example.com')->assertFailed();
        $this->assertSame(UserRole::UTILISATEUR, $user->fresh()->role);
        $this->assertSame($passwordHash, $user->fresh()->password);
    }

    public function test_invalid_email_does_not_create_an_admin(): void
    {
        $this->artisan('sentinel:admin-create invalid')->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }
}
