<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Repositories;


use App\LTI\Http\Requests\LTIRequest;
use App\Models\Activity;
use App\Models\User;
use App\LTI\Repositories\ILTIUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LTIUserRepository implements ILTIUserRepository
{


    /**
     * Creates or looks up the user based on
     * the hashed user id sent in the LTI request.
     *
     * Also associates them with the activity
     *
     *  //todo refactor this whole process to fit the laravel authentication patterns and utilities
     * @param LTIRequest $request
     * @param Activity $activity
     * @return User
     */
    public function getUserFromRequest(LTIRequest $request, Activity $activity)
    {
        $userIdHash = $request->user_id;

        try {

            //try looking them up if we've seen their id before
            $user = User::where('user_id_hash', $userIdHash)->firstOrFail();

        } catch (ModelNotFoundException $e) {
            //if they are new, we create them in the db
            $lastName = $request->lis_person_name_family;

            $firstName = $request->lis_person_name_given;


            $email = $request->has('email') ? $request->email : Str::random(100) . "@example.com";
//            $email = "currently-unusable-" . $firstName . '.' . $lastName . '@csun.edu';

            //If in demo mode everyone will be created as a chair
//            $admin = env(IS_CHAIR_DEMO, false) ? true : false;

            $user = User::create([
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_id_hash' => $userIdHash,
                'password' => Str::random(100),
//                'is_admin' => $admin
            ]);

            $user->save();
        }

        //New or old, we associate them with the activity
        $user->activities()->attach($activity);

        return $user;
    }


}
