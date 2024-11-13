<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV', 'production') == 'production') {
            // use https only if env is production
            URL::forceScheme('https');
        }
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Vérifier l\'adresse courriel')
                ->line('Cliquez sur le bouton ci-dessous pour vérifier votre adresse courriel.')
                ->action('Vérifier l\'adresse courriel', $url);
        });
    }
}
