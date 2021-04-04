<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */


namespace App\LTI\Providers;

use App\LTI\Http\Controllers\LTILaunchController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class LTIRouteServiceProvider extends RouteServiceProvider
{
    protected $namespace = 'LTI\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }


    protected function mapApiRoutes()
    {
//        Route::prefix('Mypackage\api')
//            ->middleware('api')
//            ->namespace($this->namespace)
//            ->group(__DIR__ . '\..\Routes\api.php');
    }

    protected function mapWebRoutes()
    {

        Route::post(LTILaunchController::LAUNCH_ROUTE, [LTILaunchController::class, 'handleActivityLaunchRequest'])
            ->middleware('web');
//            ->group(__DIR__ . '\..\Routes\web.php');

        //        $this->app['router']->post(LTILaunchController::LAUNCH_ROUTE,  [LTILaunchController::class, 'handleActivityLaunchRequest']);
    }
}

