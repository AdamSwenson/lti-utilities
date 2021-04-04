<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Http\Controllers;

use App\LTI\Http\Requests\LTIRequest;
use App\LTI\Authenticators\OAuthAuthenticator;
use App\LTI\Models\LTIConsumer;
use App\Models\Activity;
use App\LTI\Models\ResourceLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\helpers\LTIPayloadMaker;
use Tests\TestCase;


//use PHPUnit\Framework\TestCase;
class LTILaunchControllerTest extends TestCase
{

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $endpoint;

    public $activity;

    /**
     * @var Collection|Model
     */
    public $consumer;

    /**
     * @var Collection|Model
     */
    public $resourceLink;

    /**
     * @var string
     */
    public $urlBase;

    /**
     * @var string
     */
    public $expectedUrl;

    public $payload;

    /**
     * @var Collection|Model
     */
    public $user;

    public $object;

    public function setUp(): void
    {
        parent::setUp();
        $this->object = new LTILaunchController;

        $this->urlBase = 'http://localhost';

        $this->resourceLink = ResourceLink::factory()->create();
        $this->consumer = LTIConsumer::factory()->create();
        $this->resourceLink->ltiConsumer()->associate($this->consumer)->save();
        $this->activity = $this->resourceLink->activity;

        /** @var  path Path request goes to */
//        $this->path = "/lti/entry/" . $this->activity->id;
        $this->path = LTILaunchController::LAUNCH_ROUTE_ROOT . $this->activity->id;

        /** @var  endpoint The fully specified url used for the signature */
        $this->endpoint = $this->urlBase . $this->path;

        $this->user = User::factory()->create();
        $this->payload = LTIPayloadMaker::makePayload($this->activity, $this->endpoint, $this->resourceLink, $this->user);

        $this->expectedUrl = "/main/" . $this->activity->id;

    }



    /**
     * Using this to debug
     */
    public function testHandleLaunchRequestMockedAuth()
    {
        $authMock = Mockery::mock(OAuthAuthenticator::class)
            ->shouldReceive('authenticate')
            ->andReturn(true);

        //call
        $response = $this->post($this->path, $this->payload);

        //check
        //Check that redirected correctly
        $response->assertRedirect($this->expectedUrl);

        //Check that student logged in
        $this->assertEquals($this->user->id, Auth::id(), "Expected student logged in");

    }

    /**
     * Test directly on the controller class
     */
//    public function testHandleLaunchRequestDirect()
//    {
//        //prep
////        $request = LTIRequest::create($this->payload);
//
//        $request = new LTIRequest();
//
//        foreach ($this->payload as $k => $v) {
//            $request[$k] = $v;
//        }
//
////        $resourceLink = ResourceLink::find($data['resource_link_id']);
////        $activity = $resourceLink->activity;
//
//        //call
//        $response = $this->object->handleLaunchRequest($request, $this->activity);
//
//        //check
//        //Check that redirected correctly
//        $response->assertRedirect($this->expectedUrl);
//
//        //Check that student logged in
//        $this->assertEquals($this->user->id, Auth::id(), "Expected student logged in");
//
//
//    }


    /**
     * Tests method by making post request
     */
    public function testHandleLaunchRequestFullStack()
    {
        //prep

        //call
        $response = $this->post($this->path, $this->payload);

        //check
        //Check that redirected correctly
        $response->assertRedirect($this->expectedUrl);

        //Check that student logged in
        $this->assertEquals($this->user->id, Auth::id(), "Expected student logged in");

    }

    /*
     ================================== Newer version ===========================
    */
    /** @test  */
    public function checkLaunchRoute(){
        $expected = '/lti/entry/{activity}';

        $this->assertEquals($expected, LTILaunchController::LAUNCH_ROUTE, "launch url is correct");
    }

    /** @test  */
    public function getLaunchUrl(){
        $result = LTILaunchController::getLaunchUrl();

        $this->assertEquals(url('/lti/entry/'), $result, "Returns expected url with no activity");

        $activity = Activity::factory()->create();
        $result = LTILaunchController::getLaunchUrl($activity);

        $this->assertEquals(url("/lti/entry/{$activity->id}"), $result, "Returns expected url including activity id");



    }

    /** @test */
    public function handleActivityLaunchRequestEverythingExists()
    {
        //call
        $response = $this->post($this->path, $this->payload);

        //check
        //Check that redirected correctly
        $response->assertRedirect($this->expectedUrl);

        //Check that student logged in
        $this->assertEquals($this->user->id, Auth::id(), "Expected user logged in");

    }


    /** @test */
    public function handleActivityLaunchRequestNewResourceLink()
    {
        $this->consumer = LTIConsumer::factory()->create();
        $this->activity = Activity::factory()->create();
        $this->expectedUrl = "/main/" . $this->activity->id;

        /** @var  path Path request goes to */
        $this->path = "/lti/entry/" . $this->activity->id;

        /** @var  endpoint The fully specified url used for the signature */
        $this->endpoint = $this->urlBase . $this->path;

        $resourceLinkId = $this->faker->sha1;

        $this->user = User::factory()->create();

        $this->payload = LTIPayloadMaker::specifyPayloadContents($this->endpoint, $this->consumer->consumer_key, $this->consumer->secret_key, $resourceLinkId, $this->user->user_id_hash, []);

        //call
        $response = $this->post($this->path, $this->payload);

        //check
        //Check that redirected correctly
        $response->assertRedirect($this->expectedUrl);

        //Check that student logged in
        $this->assertEquals($this->user->id, Auth::id(), "Expected user logged in");

    }


    /** @test */
    public function handleActivityLaunchRequestBrandNew()
    {
        $this->consumer = LTIConsumer::factory()->create();
        $this->activity = Activity::factory()->create();
        $this->expectedUrl = "/main/" . $this->activity->id;

        /** @var  path Path request goes to */
        $this->path = "/lti/entry/" . $this->activity->id;

        /** @var  endpoint The fully specified url used for the signature */
        $this->endpoint = $this->urlBase . $this->path;

        $resourceLinkId = $this->faker->sha1;

        $userId = $this->faker->sha1;

        $this->payload = LTIPayloadMaker::specifyPayloadContents($this->endpoint, $this->consumer->consumer_key, $this->consumer->secret_key, $resourceLinkId, $userId, []);

        //call
        $response = $this->post($this->path, $this->payload);

        //check
        //Check that redirected correctly
        $response->assertRedirect($this->expectedUrl);

        //Check that student logged in
        $loggedIn = Auth::user();
        $this->assertEquals($userId, $loggedIn->user_id_hash, "Expected user logged in");

    }


}
