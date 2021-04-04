<?php

namespace Database\Seeders;

use App\LTI\Models\LTIConsumer;
use App\Models\Activity;
use App\LTI\Models\ResourceLink;
use Illuminate\Database\Seeder;

class LTIDevCredsSeeder extends Seeder
{
    /**
     * Creates the keys used in the LTI authentication
     * process on the first activity in the database.
     *
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $activitys = Activity::all();
        if (! isset($activitys)) {
            $activity = Activity::factory()->create();
        }else {
            $activity = $activitys[0];
        }

        //todo Set up commented part instead so will only have one entry
//        $name = 'Development consumer';
//
//        $consumer = LTIConsumer::where('consumer_key', env('DEV_CONSUMER_KEY'))
//            ->where('secret_key', env('DEV_SHARED_KEY'))
//            ->where('name', $name)->firstOrCreate();

//        ResourceLink::where('activity_id', $activity->id)
//            ->where('resource_link_id', env('DEV_RESOURCE_LINK_ID'))
//            ->where('lti_consumer_id',  $consumer->id)
//            ->where('description', $activity->name)
//            ->firstOrCreate();


        $consumer = LTIConsumer::factory([
                'name' => 'Development consumer',
                'consumer_key' => env('DEV_CONSUMER_KEY'),
                'secret_key' => env('DEV_SHARED_KEY')
            ])->create();


        ResourceLink::factory([
            'activity_id' => $activity->id,
            'resource_link_id' => env('DEV_RESOURCE_LINK_ID'),
            'lti_consumer_id' => $consumer->id
        ])->create();




        //
    }
}
