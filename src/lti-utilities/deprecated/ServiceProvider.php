<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI;

//namespace RobertBoes\LaravelLti;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**\
 * Class ServiceProvider
 * Copied from RobertBoes\LaravelLti\ToolProvider so can customize
 *
 *
 * @deprecated
 *
 * @package App\LTI
 */
class ServiceProvider extends IlluminateServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-lti.php' => config_path('laravel-lti.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('/migrations'),
        ],'migrations');
    }

    public function register()
    {
        $this->commands([
            \RobertBoes\LaravelLti\Commands\CreateToolConsumerCommand::class
        ]);
    }
}
