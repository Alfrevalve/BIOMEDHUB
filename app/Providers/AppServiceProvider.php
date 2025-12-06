<?php

namespace App\Providers;

use App\Models\CirugiaReporte;
use App\Models\Item;
use App\Models\ItemKit;
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
        // Registrar observer de Cirugias
        \App\Models\Cirugia::observe(\App\Observers\CirugiaObserver::class);
        \App\Models\Movimiento::observe(\App\Observers\MovimientoObserver::class);

        // Policies para reforzar acceso por rol
        Gate::policy(Item::class, \App\Policies\ItemPolicy::class);
        Gate::policy(ItemKit::class, \App\Policies\ItemKitPolicy::class);
        Gate::policy(CirugiaReporte::class, \App\Policies\CirugiaReportePolicy::class);
    }
}
