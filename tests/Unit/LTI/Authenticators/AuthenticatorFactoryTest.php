<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace Tests\LTI\Authenticators;

use App\LTI\Authenticators\AuthenticatorFactory;

//use PHPUnit\Framework\TestCase;
use App\LTI\Authenticators\OAuthAuthenticator;
use Tests\helpers\LTIRequestMaker;
use Tests\TestCase;

class AuthenticatorFactoryTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();


    }

    public function testMake()
    {
        $request = LTIRequestMaker::makeRequest();
        $result = AuthenticatorFactory::make($request);

        $this->assertInstanceOf(OAuthAuthenticator::class, $result, "Expected type returned");
        //todo NB Not actually acting as factory yet

    }

}
