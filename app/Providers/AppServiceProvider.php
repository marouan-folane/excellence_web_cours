<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Providers\PlainTextPasswordProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No need to manually bind the PDF facade as it's already registered by the service provider
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the plain text password provider
        Auth::provider('plaintext', function ($app, array $config) {
            return new PlainTextPasswordProvider($app['hash'], $config['model']);
        });
        
        // Set the application locale from session on every request
        $this->setLocaleFromSession();
    }

    /**
     * Set application locale from session
     * 
     * @return void
     */
    protected function setLocaleFromSession()
    {
        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        }
    }
}
