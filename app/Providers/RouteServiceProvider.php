<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Este método se ejecuta durante el arranque de la aplicación.
     */
    public function boot(): void
    {
        $this->routes(function () {

            // Rutas API con prefijo /api
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Rutas web sin prefijo
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
