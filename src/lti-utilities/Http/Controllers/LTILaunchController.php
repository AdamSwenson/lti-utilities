<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Http\Controllers;

use App\Http\Controllers\Controller;

use App\LTI\Exceptions\InvalidLTILogin;
use App\LTI\Exceptions\LTIAuthenticationException;

use App\LTI\Repositories\ILTIRepository;
use App\LTI\Repositories\ILTIUserRepository;
use App\LTI\Http\Requests\LTIRequest;

use App\Models\Activity;

//use App\Repositories\LTI\ILTIRepository;
//use App\Repositories\ILTIUserRepository;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//use App\Providers\LTIServiceProvider;


/**
 * Class LTILaunchController
 *
 * Handles requests from the LMS
 * to launch an activity via LTI.
 *
 * @package App\Http\Controllers
 */
class LTILaunchController extends Controller
{

    /**
     * This is the common path which all launch requests will share.
     * It is only relevant for defining the settings when configuring the LMS
     * and in testing
     */
    const LAUNCH_ROUTE_ROOT = '/lti/entry/';

    /**
     * This is the actual route used that this controller will be bound to.
     */
    const LAUNCH_ROUTE = self::LAUNCH_ROUTE_ROOT . '{activity}';

//
//    /**
//     * @var LTI|mixed
//     */
//    public $lti;

    /**
     * @var ILTIRepository
     */
    public $LTIRepository;

    /**
     * @var ILTIUserRepository
     */
    public $userRepository;

    /**
     * LTILaunchController constructor.
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        //create an lti object to use by instantiating via
        //the lti service provider
//        $this->lti = app()->make(LTI::class);

        //we do this here since type hinting messes with the tests
        $this->LTIRepository = app()->make(ILTIRepository::class);
        $this->userRepository = app()->make(ILTIUserRepository::class);
    }


    /**
     * Returns the url for launching the app from the LMS
     * via LTI
     *
     * @param null $activity
     * @return string
     */
    static public function getLaunchUrl($activity = null)
    {
        $path = self::LAUNCH_ROUTE_ROOT;
        if (!is_null($activity)) {
            $path = "{$path}{$activity->id}";
        }
        return url($path);
    }

    /**
     * Receives the launch request from a link
     * which contains the activity id.
     *
     * This is the newer version.
     *
     *
     * @param LTIRequest $request
     * @param Activity $activity
     * @return void
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function handleActivityLaunchRequest(LTIRequest $request, Activity $activity)
    {

        //todo remove
        Log::debug("=========== LTILaunchController@handleActivityLaunchRequest ===========");
        Log::debug($request->url());
        Log::debug($request);


        //Check if the activity is enabled and reject access if not
        //todo Do this (later) or maybe add as middleware

        //The LTIRequest object has already checked that the needed
        //fields are populated.
        try {

            $this->LTIRepository->handleActivityLaunchRequest($request, $activity);

            //Get an existing user or create a new person in the db
            $user = $this->userRepository->getUserFromRequest($request, $activity);

            //Log them in
            Auth::login($user, true);

            //We redirect to the main app page
            return redirect()->route('main', $activity->id);

        } catch (LTIAuthenticationException $e) {
            Log::debug($e);

            abort(403, 'Unauthorized action.');

        } catch (InvalidLTILogin $e) {
            Log::debug($e);

            abort(411, 'Unauthorized action.');
        }

    }

}




//    /**
//     * Receives the launch request
//     * @deprecated
//     *
//     * lti:create-tool-consumer
//     *  key: tacokey
//     *  name: taco
//     * secret: nom
//     * @param LTIRequest $request
//     * @return void
//     */
//    public function handleLaunchRequest(LTIRequest $request)
//    {
//        $j = $request->all();
//
//        //todo remove
//        Log::debug($request);
//
//        //Check if the activity is enabled and reject access if not
//        //todo Do this (later) or maybe add as middleware
//
//        //The LTIRequest object has already checked that the needed
//        //fields are populated.
//        try {
//
//            //We verify that the oath signature on the incoming post
//            //request is valid
//            $resourceLink = ResourceLink::where(['resource_link_id' => $request->resource_link_id])
//                ->firstOrFail();
//            //todo error handling if not found
//
//            $authenticator = AuthenticatorFactory::make($request);
//            $authenticator->authenticate($request, $resourceLink);
//
//            //Get an existing user or create a new person in the db
//            $this->handleUser($request);
//
//            //We redirect to the activity page
//            return redirect()->route('activityHome', $resourceLink->activity);
//
//        } catch (LTIAuthenticationException $e) {
//            Log::debug($e);
//
//            abort(403, 'Unauthorized action.');
//
//        }
//
//    }


//    /**
//     * Creates or looks up the user and logs them
//     * in.
//     *
//     * @deprecated Replaced with UserRepository
//     *
//     *  //todo refactor this whole process to fit the laravel authentication patterns and utilities
//     * @param LTIRequest $request
//     */
//    protected function handleUser(LTIRequest $request)
//    {
//        $lastName = $request->lis_person_name_family;
//
//        $firstName = $request->lis_person_name_given;
//
//        $userIdHash = $request->user_id;
//
//        //try looking them up if we've seen their id before
//        try {
//            $this->user = User::where('user_id_hash', $userIdHash)->firstOrFail();
//        }catch(ModelNotFoundException $e){
//
//            $email = "currently-unusable-" . $firstName . '.' . $lastName . '@csun.edu';
//
//            $this->user = User::create([
//                'email' => $email,
//                'first_name' => $firstName,
//                'last_name' => $lastName,
//                'user_id_hash' => $userIdHash
//            ]);
//
////            $this->user->first_name = $firstName;
////            $this->user->last_name = $lastName;
////            $this->user->user_id_hash = $userIdHash;
//
//            $this->user->save();
//        }
//
//        //associate them with the activity
//        //todo
//
//        Auth::login($this->user, true);
//
//
//    }


//    /**
//     * Receives the launch request
//     *
//     * lti:create-tool-consumer
//     *  key: tacokey
//     *  name: taco
//     * secret: nom
//     * @param LTIRequest $request
//     * @return void
//     */
//    public function handleLaunchRequest(LTIRequest $request, Activity $activity)
//    {
//        //Check if the activity is enabled and reject access if not
//        //todo Do this (later) or maybe add as middleware
//
//        //The LTIRequest object has already checked that the needed
//        //fields are populated.
//        try {
//
//            //We verify that the oath signature on the incoming post
//            //request is valid
//            $resourceLink = ResourceLink::where(['id' => $request->resource_link_id])
//                ->firstOrFail();
//            //todo error handling if not found
//
//            $authenticator = AuthenticatorFactory::make($request);
//            $authenticator->authenticate($request, $resourceLink);
//
//            //Now we look up the student based on the provided user id
//            //todo consider whether want to just add anyone who passes the oath hurdle or if want whitelisted students (if latter, this would be firstOrFail)
//            $user = User::where(['lms_id' => $request->user_id])
//                ->firstOrFail();
//
//            //we log them in via the usual laravel means
//            //todo refactor this whole process to fit the laravel authentication patterns and utilities
//            Auth::login($user, true);
//
//            //We redirect to the activity page
//            return redirect()->route('activityHome', [$activity]);
//
//        } catch (LTIAuthenticationException $e) {
//            Log::debug($e);
//
//            abort(403, 'Unauthorized action.');
//            //todo logging and error handling here
//
//        }
//
//    }
//
//    /**
//     * Receives the launch request
//     *
//     * lti:create-tool-consumer
//     *  key: tacokey
//     *  name: taco
//     * secret: nom
//     * @param Request $request
//     * @return ResponseFactory|Response
//     */
//    public function handleLaunchRequestUsingLibrary(Request $request)
//    {
////        //push incoming data into global post array
////        //so lti can operate on it.
////        $this->populatePostArray($request);
////
////        $this->populateServerArray($request);
////
////        $this->lti->toolProvider()->handleRequest();
////
////
////        if ($this->lti->toolProvider()->ok !== true) {
////            $t = 'nom3';
////            dump($t);
////        }
//////        dd($prov->resourceLink);
//////        dd($prov);
//////        $LTIToolProvider->handleRequest();
//////dd($request);
//////        dd(LTI::toolProvider()->reason);
////
//////        return response('sdf', 200);
////
//////        return response()->json([
//////            'name' => 'Abigail',
//////            'state' => 'CA',
//////        ]);
////
////        return response('j', 200);
//
//    }
//
//    /**
//     * The library handles authentication in the old school way
//     * so we need to take the hopefully clean request and
//     * shove it into the global array.
//     * @param Request $request
//     * @todo Long term: think through whether worth rewriting the library class is worthwhile.
//     */
//    public function populatePostArray(Request $request): void
//    {
//        foreach ($request->all() as $k => $v) {
//            //The library handles authentication in the old school way
//            //so we need to take the hopefully clean request and
//            //shove it into the global array
//            $_POST[$k] = $v;
//        }
//    }
//
//    public function populateServerArray(Request $request): void
//    {
//        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
//        $_SERVER['HTTP_HOST'] = 'www.test.com';
//        $_SERVER['REQUEST_URI'] = '/test';
//        $_SERVER['REQUEST_METHOD'] = 'POST';
//
//    }

