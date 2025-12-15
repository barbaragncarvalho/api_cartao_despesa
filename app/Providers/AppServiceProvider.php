<?php

namespace App\Providers;

use App\Models\Cartao;
use App\Models\Despesa;
use App\Policies\CartaoPolicy;
use App\Policies\DespesaPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Cartao::class, CartaoPolicy::class);
        Gate::policy(Despesa::class, DespesaPolicy::class);
    }
}
