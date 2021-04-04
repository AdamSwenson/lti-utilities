<?php


namespace App\LTI\Authenticators;


use App\LTI\Http\Requests\LTIRequest;
use App\LTI\Authenticators\LaunchAuthenticator;
use App\LTI\Authenticators\OAuthAuthenticator;

/**
 * Class AuthenticatorFactory
 *
 * Determines the correct type of authenticator to use and returns
 * it. This is a 'factory' in the sense of the factory design pattern.
 * It is not a 'factory' in the sense of laravel's test model generator.
 *
 * @todo Does not act as a factory yet. Just returns OAuthAuthenticator
 *
 * @package App\LTI\Authenticators
 */
class AuthenticatorFactory
{

    public static function make( $request=null){

        return new OAuthAuthenticator();

        //check from the type of request

        if($request instanceof LTIRequest){

            switch($request->lti_message_type){
                case 'basic-lti-launch-request':
                    return new LaunchAuthenticator($request);
            }
        }


    }

}
