<?php


namespace Tests\helpers;


use App\LTI\Http\Requests\LTIRequest;

use App\LTI\Models\LTIConsumer;
use App\Models\Activity;
use App\LTI\Models\ResourceLink;
use App\Models\User;
use Illuminate\Validation\Rule;
use Faker;
class LTIRequestMaker
{




    public static function makeRequest($resourceLink=null, $user=null, $endpoint = 'https://www.test.com/test', $method='POST'){
        $faker = Faker\Factory::create();

        $activity = Activity::factory()->create();
        $user = User::factory()->create();

        $oauthPayload = LTIPayloadMaker::makePayload($activity);

//        if(is_null($resourceLink)){
//            $resourceLink = ResourceLink::factory()->create();
//            $consumer = LTIConsumer::factory()->create();
//            $resourceLink->ltiConsumer()->associate($consumer)->save();
//        }
//
//        //if we just got the id as resource link
//        $resourceLinkId = is_integer($resourceLink) ? $resourceLink : $resourceLink->id;
//
//        $user = !is_null($user) ? $user : User::factory()->create();

        $other = [
////            /* Oauth stuff */
//            'oauth_consumer_key' => $resourceLink->ltiConsumer->consumer_key,
//            'oauth_nonce' => $faker->sha1(),
//            'oauth_signature' => $faker->sha1(),
//            'oauth_signature_method' => ['required'],
//            'oauth_timestamp' => ['required'],
//            'oauth_version'  => ['required'],
//
//            /* LTI */
//            'lti_message_type' => LTIRequest::$MESSAGE_TYPES[0],
//            'lti_version' => LTIRequest::$LTI_VERSIONS[0],
//
//            /* The Canvas assignment which is linking to the activity */
//            'resource_link_id' => $resourceLinkId,
//            'resource_link_title' => $resourceLink->description,
//
//            /* The user's info */
//            'user_id' => $user->user_id_hash,
            'lis_person_name_family' => $user->first_name,
            'lis_person_name_given' => $user->last_name

        ];

        $p = array_merge($oauthPayload, $other);

        return LTIRequest::create($endpoint, $method, $p);

    }
}
