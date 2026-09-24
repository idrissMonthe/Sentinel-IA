<?php

namespace App\Providers;

use App\Services\Analyse\AnalyseIAService;
use App\Services\Analyse\OpenAIAnalyseIAService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
{
    ResetPassword::toMailUsing(function (object $notifiable, string $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe Sentinel IA')
            ->view('emails.reset-password', ['url' => $url, 'user' => $notifiable]);
    });
}

    public function register(): void
    {
        $this->app->bind(AnalyseIAService::class, OpenAIAnalyseIAService::class);
    }
}
