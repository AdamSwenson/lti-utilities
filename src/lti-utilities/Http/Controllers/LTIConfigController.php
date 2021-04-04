<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class LTIConfigController
 *
 * Created in PWB-9
 *
 * @package App\LTI\Http\Controllers
 */
class LTIConfigController extends Controller
{

    /**
     * Returns an LTI xml configuration file.
     * todo NOT USED. FILE IS NOT CORRECT
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function lticonfig(Request $request)
    {
        Log::debug($request->all());

        return response(file_get_contents(resource_path('lticonfig.xml')), 200, [
            'Content-Type' => 'application/xml'
        ]);

//        return view('lticonfig');
    }
}
