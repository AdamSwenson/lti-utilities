<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Repositories;

use App\LTI\Http\Requests\LTIRequest;

use App\Models\Activity;
use App\Models\User;

interface ILTIUserRepository
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
    public function getUserFromRequest(LTIRequest $request, Activity $activity);
}
