<?php
namespace App\LTI\Http\Requests;

use Tests\TestCase;

use Tests\helpers\LTIPayloadMaker;

class LTIRequestTest extends TestCase
{


    public function setUp(): void
    {
        parent::setUp();
        $this->object = new LTIRequest();
    }

    public function testGet_signable_parameters()
    {
        $this->markTestSkipped('method probably deprecated and unused');

        //prep
        $payload = LTIPayloadMaker::makePayload();

        foreach($payload as $k=>$v){
            $this->object[$k] = $v;
        }

        //call
        $result = $this->object->get_signable_parameters();

        //check

        foreach($payload as $k=>$v){
            if($k !== 'oauth_signature'){
                $this->assertStringContainsString($k, $result, "Result contains key $k");
            }
        }

        //we have to do it by checking for the value since
        //the expected presence of oauth_signature_method will
        //prevent us from searching the value
        $this->assertStringNotContainsString($payload['oauth_signature'], $result, "check that oauth_signature is removed");


    }

//    public function testGet_signature_base_string()
//    {
//
//    }
//
//    public function testGet_normalized_http_url()
//    {
//
//    }

}
