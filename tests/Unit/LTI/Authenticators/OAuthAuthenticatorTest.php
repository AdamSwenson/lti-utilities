<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Tests\LTI\Authenticators;

use App\LTI\Http\Requests\LTIRequest;
use App\LTI\Authenticators\OAuth\OAuthSignatureMethod;
use App\LTI\Authenticators\OAuth\OAuthSignatureMethod_HMAC_SHA1;
use App\LTI\Authenticators\OAuthAuthenticator;

//use PHPUnit\Framework\TestCase;
use App\LTI\Exceptions\OAuthException;
use App\LTI\Models\UsedNonce;
use App\LTI\Models\ResourceLink;
use Mockery;
use Tests\helpers\LTIRequestMaker;
use Tests\TestCase;

class OAuthAuthenticatorTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->object = new OAuthAuthenticator();

    }

    /** @test */
    public function isNonceNew_happy_path()
    {
        $request = new LTIRequest();
        $request->oauth_nonce = $this->faker->randomNumber(8);

        $result = $this->object->isNonceNew($request);

        $this->assertTrue($result, "Identifies new nonce");
    }


    /** @test */
    public function isNonceNew_previously_used()
    {
        $this->expectException(OAuthException::class);

        $used = UsedNonce::factory()->create();
        $request = new LTIRequest();
        $request->oauth_nonce = $used->nonce;

        $result = $this->object->isNonceNew($request);

    }

    /** @test */
    public function getSignatureMethod_happy_paths()
    {

        foreach ($this->object->supportedSignatureMethods as $k => $v) {
            $request = new LTIRequest();
            $request->oauth_signature_method = $k;

            $this->object->getSignatureMethod($request);

            $this->assertEquals($v, $this->object->signatureMethod, "Signature method object set");
        }
    }

    /** @test */
    public function getSignatureMethod_unsupported_method()
    {

        $this->expectException(OAuthException::class);

        $request = new LTIRequest();
        $request->oauth_signature_method = 'tacoSHA2';

        $this->object->getSignatureMethod($request);

    }

    /** @test */
    public function isTimestampNewEnough_happy_path()
    {
        $request = new LTIRequest();
        $request->oauth_timestamp = time();

        $result = $this->object->isTimestampNewEnough($request);

        //check
        $this->assertTrue($result, "Returns true when timestamp is current");
    }

    /** @test */
    public function isTimestampNewEnough_too_old()
    {
        $this->expectException(OAuthException::class);

        $request = new LTIRequest();
        $old = time() - $this->object->timestamp_threshold - 100;
        $request->oauth_timestamp = $old;

        $this->object->isTimestampNewEnough($request);
    }

    public function testIs_signature_valid()
    {
        $request = LTIRequestMaker::makeRequest();
        $resourceLink = ResourceLink::factory()->create();

        $signatureMock = Mockery::mock(OAuthSignatureMethod::class);
            $signatureMock->shouldReceive('check_signature')
                ->with($request, $resourceLink)
                ->andReturn(true);
        $this->object->signatureMethod = $signatureMock;

        $result = $this->object->isSignatureValid($request, $resourceLink);
        $this->assertTrue($result);
    }

    /** @test */
    public function authenticate_with_signature_method_mocked()
    {

        $resourceLink = ResourceLink::factory()->create();

        $request = LTIRequestMaker::makeRequest($resourceLink);

        $signatureMock = Mockery::mock(OAuthSignatureMethod::class);
        $signatureMock->shouldReceive('check_signature')
            ->with($request, $resourceLink)
            ->andReturn(true);

        $this->object->supportedSignatureMethods['HMAC-SHA1'] = $signatureMock;

        $result = $this->object->authenticate($request, $resourceLink);

        $this->assertTrue($result);


    }

    /** @test */
    public function checkVersion_happy_path()
    {
        foreach (OAuthAuthenticator::SUPPORTED_VERSIONS as $v) {
            $request = new LTIRequest();
            $request->oauth_version = $v;

            $this->object->checkVersion($request);

            $this->assertEquals($v, $this->object->version, "Version set on object");


        }

    }

    /** @test */
    public function checkVersion_unspecified_version()
    {
        foreach (OAuthAuthenticator::SUPPORTED_VERSIONS as $v) {
            $request = new LTIRequest();
            $this->object->checkVersion($request);

            $this->assertEquals('1.0', $this->object->version, "Default Version set on object");
        }
    }

    /** @test */
    public function checkVersion_invalid_version()
    {
        $this->expectException(OAuthException::class);
        $request = new LTIRequest();
        $request->oauth_version = '7.3902';

        $this->object->checkVersion($request);
    }
}
