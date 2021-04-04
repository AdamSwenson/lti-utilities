<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Tests\Repositories\LTI;

use App\Models\Activity;

use App\LTI\Http\Requests\LTIRequest;
use App\LTI\Models\LTIConsumer;
use App\LTI\Models\ResourceLink;
use App\LTI\Repositories\LTIRepository;

use Illuminate\Support\Str;
use Tests\TestCase;


class LTIRepositoryTest extends TestCase
{
    public function setUp():void
    {
        parent::setUp();

        $this->object = new LTIRepository();
    }

    /** @test */
    public function generateConsumerKey()
    {
        $result = LTIRepository::generateConsumerKey();
        $this->assertEquals(LTIRepository::KEY_LENGTH, Str::of($result)->length(), "Key is correct length");
    }

    /** @test */
    public function generateSecretKey()
    {
        $result = LTIRepository::generateSecretKey();
        $this->assertEquals(LTIRepository::KEY_LENGTH, Str::of($result)->length(), "Key is correct length");
    }


    /** @test */
    public function createLTIConsumer()
    {
        $name = $this->faker->company;

        $result = $this->object->createLTIConsumer($name);

        $this->assertInstanceOf(LTIConsumer::class, $result);
        $this->assertEquals(LTIRepository::KEY_LENGTH, Str::of($result->consumer_key)->length(), "Consumer key is correct length");
        $this->assertEquals(LTIRepository::KEY_LENGTH, Str::of($result->secret_key)->length(), "Secret key is correct length");
    }

    /** @test */
    public function createResourceLinkEntry()
    {
        $description = $this->faker->paragraph;
        $activity = Activity::factory()->create();
        $consumer = LTIConsumer::factory()->create();
        $resourceLinkId = $this->faker->sha1;

        $result = $this->object->createResourceLinkEntry($consumer, $activity, $resourceLinkId, $description);

        $this->assertInstanceOf(ResourceLink::class, $result);
        $this->assertEquals($resourceLinkId, $result->resource_link_id, "Incoming link id was store");
        $this->assertEquals($activity->id, $result->activity_id);
        $this->assertEquals($consumer->id, $result->lti_consumer_id);
        $this->assertEquals($description, $result->description);
    }

//
//    /** @test */
//    public function handleResourceLinkInRequestExistingModel()
//    {
//        $request = new LTIRequest();
//        $resourceLink = ResourceLink::factory()->create();
//        $request['resource_link_id'] = $resourceLink->resource_link_id;
//
//        //call
//        $result = $this->object->getResourceLinkFromRequest($request);
//
//        //check
//        $this->assertTrue($resourceLink->is($result), "Correct model returned" );
//
//    }


    /** @test */
    public function getResourceLinkFromRequestExistingModel()
    {
        $request = new LTIRequest();
        $resourceLink = ResourceLink::factory()->create();
        $request['resource_link_id'] = $resourceLink->resource_link_id;
        $activity = Activity::factory()->create();

        //call
        $result = $this->object->getResourceLinkFromRequest($request, $activity);

        //check
        $this->assertTrue($resourceLink->is($result), "Correct model returned" );

    }

    /** @test */
    public function getResourceLinkFromRequestWhenNoExistingModel()
    {
        $id = $this->faker->sha1;
        $description = $this->faker->company;
        $consumer = LTIConsumer::factory()->create();

        $request = new LTIRequest();
        $request['resource_link_id'] = $id;
        $request['resource_link_title'] = $description;
        $request['oauth_consumer_key'] = $consumer->consumer_key;
        $activity = Activity::factory()->create();

        //call
        $result = $this->object->getResourceLinkFromRequest($request, $activity);

        //check
        $this->assertDatabaseHas('resource_links', [
            'resource_link_id' => $id,
            'lti_consumer_id' => $consumer->id,
            'activity_id' => $activity->id,
            'description' => $description
        ]);

        $this->assertInstanceOf(ResourceLink::class, $result, "Returns expected object");
    }
}
