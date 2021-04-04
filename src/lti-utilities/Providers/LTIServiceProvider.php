<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Providers;


use App\LTI\Repositories\ILTIRepository;
use App\LTI\Repositories\LTIRepository;

use App\LTI\Repositories\LTIUserRepository;
use App\LTI\Repositories\ILTIUserRepository;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class LTIServiceProvider
 *
 * Provides access to the LTI class which handles
 *handles lti logins
 *
 * @package App\Providers
 */
class LTIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        //Repositories
        $this->app->bind(ILTIRepository::class, LTIRepository::class);

        $this->app->bind(ILTIUserRepository::class, LTIUserRepository::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //Register lti access routes
        $this->app->register(LTIRouteServiceProvider::class);


    }
}
