<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Tests\LTI\Repositories;

use App\LTI\Http\Requests\LTIRequest;
use App\Models\Activity;
use App\Models\User;
use App\LTI\Repositories\LTIUserRepository;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new LTIUserRepository();

    }

    public function testGetUserFromRequest()
    {
        $request = new LTIRequest();
        $request->lis_person_name_family = $this->faker->lastName;
        $request->lis_person_name_given = $this->faker->firstName;
        $request->user_id = $this->faker->sha1;
        $activity = Activity::factory()->create();

        //call
        $result = $this->object->getUserFromRequest($request, $activity);

        //check
        $this->assertInstanceOf(User::class, $result);

        $this->assertDatabaseHas('activity_user', ['activity_id' => $activity->id,
            'user_id' => $result->id]);

    }

}
