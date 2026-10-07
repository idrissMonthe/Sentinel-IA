<?php

namespace Tests\Feature;

use App\Mail\CompteSupprimeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_supprimer_son_compte_et_recoit_un_email(): void
    {
        Mail::fake();
        $user = User::factory()->create(['password' => 'motdepasse123']);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'motdepasse123',
        ]);

        $response->assertRedirect(route('accueil'));
        $this->assertGuest();
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        Mail::assertSent(CompteSupprimeMail::class, function (CompteSupprimeMail $mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_la_suppression_est_refusee_avec_un_mot_de_passe_incorrect(): void
    {
        Mail::fake();
        $user = User::factory()->create(['password' => 'motdepasse123']);

        $response = $this->actingAs($user)->from(route('profile.edit'))->delete(route('profile.destroy'), [
            'password' => 'incorrect',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
        Mail::assertNothingSent();
    }
}
