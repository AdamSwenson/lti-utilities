<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Database\Factories\LTI\Models;

use App\LTI\Models\UsedNonce;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsedNonceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UsedNonce::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nonce' => $this->faker->randomNumber(8),
            'expires' => $this->faker->dateTime
        ];
    }
}
