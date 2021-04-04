<?php

namespace Tests;

use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //39s for 121 tests without
    use RefreshDatabase;

    use CreatesApplication;

    public $object;

    /**
     * @var \Faker\Generator
     */
    public $faker;

    public $studentUser;

    public $teacherUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->studentUser = User::factory()->student()->create();
        $this->teacherUser = User::factory()->teacher()->create();


    }
}
