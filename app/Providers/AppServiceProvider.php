<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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
        //
        // Define a regra 'username' para validar o formato usuario@empresa
        Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
            // Permite alfanuméricos e o @
            return preg_match('/^[a-zA-Z0-9_@]+$/', $value);
        });
    }
}
