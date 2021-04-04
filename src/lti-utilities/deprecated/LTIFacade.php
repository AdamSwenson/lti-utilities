<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI;


use Illuminate\Support\Facades\Facade as IlluminateFacade;

/**
 * todo Does not work
 * Class Facade
 * Provides facade access to LTI
 *
 * @deprecated
 *
 * Copied from RobertBoes\LaravelLti\ToolProvider so can customize
 * @package App\LTI
 */
class LTIFacade extends IlluminateFacade
{

    protected static function getFacadeAccessor()
    {
        return 'LTI';
    }
}
