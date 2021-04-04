<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Database\Factories\LTI\Models;
use Illuminate\Database\Eloquent\Factories\Factory;


use App\LTI\Models\LTIConsumer;
use Faker\Generator as Faker;


class LTIConsumerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LTIConsumer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name' => $this->faker->company,
            'consumer_key' => $this->faker->sha1,
            'secret_key' => $this->faker->sha1,
        ];
    }
}
