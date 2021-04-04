<?php


namespace App\LTI\Providers;


//use App\LTI\LTI;
//use App\LTI\ToolProvider\ToolProvider;
use App\LTI\Repositories\ILTIRepository;
use Tests\TestCase;

class LTIServiceProviderTest extends TestCase
{

    public $object;

    public function setUp(): void
    {
        parent::setUp();
    }


    public function testServiceProviderRegistersRepositories()
    {
//        $this->markTestSkipped('Not using the service provider at this time');

        $repo = app()->make(ILTIRepository::class);
        $this->assertInstanceOf(ILTIRepository::class, $repo, "LTIRepository registered ");

    }

}
