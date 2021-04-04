<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Database\Factories\LTI\Models;

use App\LTI\Models\LTIConsumer;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Activity;
use App\Model;
use App\LTI\Models\ResourceLink;

class ResourceLinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResourceLink::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        //create a new activity and link it via the id
        $activity = Activity::factory()->create();
        $consumer = LTIConsumer::factory()->create();

        return [
            'activity_id' => $activity->id,

            'resource_link_id' => $this->faker->sha1,

            'description' => $this->faker->sentence,

            'lti_consumer_id' => $consumer->id
        ];
    }
}
