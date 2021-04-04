<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Tests\LTI\Commands;

use App\LTI\Commands\CreateLTIConsumer;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class CreateLTIConsumerTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

    }

    public function testHandle()
    {
        $this->markTestSkipped('TODO whenever I feel like doing very trivial tests');
        $this->object = new CreateLTIConsumer();

        $result = $this->object->handle();

        $this->assertEquals(0, $result);
    }

}
