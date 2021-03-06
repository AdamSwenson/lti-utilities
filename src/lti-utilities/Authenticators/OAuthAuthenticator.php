<?php


namespace App\LTI\Authenticators;


use App\LTI\Http\Requests\LTIRequest;
use App\LTI\Authenticators\OAuth\OAuthSignatureMethod;
use App\LTI\Authenticators\OAuth\OAuthSignatureMethod_HMAC_SHA1;
use App\LTI\Authenticators\OAuth\OAuthSignatureMethod_HMAC_SHA256;

use App\LTI\Authenticators\OAuthUtil;
use App\LTI\Exceptions\InvalidLTILogin;
use App\LTI\Exceptions\OAuthException;
use App\Models\Activity;
use App\LTI\Models\ResourceLink;
use App\LTI\Models\UsedNonce;
use Illuminate\Support\Facades\Log;


/**
 * Class OAuthAuthenticator
 * Based on  IMSGlobal\LTI\OAuthServer
 * @package App\LTI\Authenticators
 *
 */
class OAuthAuthenticator implements IAuthenticator
{

    const SUPPORTED_VERSIONS = ['1.0'];

    public $version = '1.0';             // hi blaine

    public $timestamp_threshold = 300; // in seconds, five minutes


    /**
     * Holds signature method objects as values with their names as
     * keys. Populated during object construction
     * @var array
     */
    public $supportedSignatureMethods = [];

    /**
     * The signature method object to use, set from the LTI request
     *
     * @var null|OAuthSignatureMethod
     */
    public $signatureMethod = null;

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var mixed
     */
    protected $ltiConsumer;

    public function __construct( )
    {
        //instantiate the available signature classes
        $this->supportedSignatureMethods = [
            "HMAC-SHA1" => new OAuthSignatureMethod_HMAC_SHA1(),
            "HMAC-SHA256" => new OAuthSignatureMethod_HMAC_SHA256()
        ];
    }

    /**
     * Handles authentication of oauth requests.
     *
     * NB, Translates any internal exceptions into a generic
     * InvalidLTILogin
     *
     * @param LTIRequest $request
     * @param ResourceLink $resourceLink
     * @return bool
     * @throws InvalidLTILogin
     */
    public function authenticate(LTIRequest $request, ResourceLink $resourceLink)
    {

//        $this->activity = $resourceLink->activity();
//        $this->ltiConsumer = $resourceLink->ltiConsumer;

        try {
            //figure out what we're dealing with
            $this->getSignatureMethod($request);

            $this->checkVersion($request);

            //Validate fields (these return true or throw
            // an oauth exception otherwise)
            $this->isTimestampNewEnough($request);
            $this->isNonceNew($request);

            //Validate signature
            return $this->isSignatureValid($request, $resourceLink);

        } catch (OAuthException $oae) {
            //Do any cleanup necessary

            //Translate to the more generic exception that the calling method will expect
            throw new InvalidLTILogin($oae->getMessage());
        }
    }

    // Figuring out what we're dealing with

    /**
     * Examines the request and determines what
     * signature method is needed
     *
     * NB, the request has verified that the field is present
     * @param LTIRequest $request
     * @throws OAuthException
     */
    public function getSignatureMethod(LTIRequest $request)
    {
        $method_name = $request->oauth_signature_method;

        //Check if the method is supported
        if (!in_array($method_name,
            array_keys($this->supportedSignatureMethods))) {
            throw new OAuthException(
                "Signature method '$method_name' not supported ");
        }

        //set signature method that we'll use
        $this->signatureMethod = $this->supportedSignatureMethods[$method_name];;
    }


    /**
     * Verifies that the oath version is supported
     * version 1
     * @param LTIRequest $request
     * @throws OAuthException
     */
    public function checkVersion(LTIRequest $request)
    {
        $version = $request->oauth_version;
        if (!$version) {
            // Service Providers MUST assume the protocol version to be 1.0 if this parameter is not present.
            // Chapter 7.0 ("Accessing Protected Ressources")
            $version = '1.0';
        }
        if (!in_array($version, self::SUPPORTED_VERSIONS)) {
            throw new OAuthException("OAuth version '$version' not supported");
        }

        //set to current version
        $this->version = $version;
    }


    /**
     * check that the timestamp is new enough.
     * NB, the request has verified that the field is present
     * @param LTIRequest $request
     * @return bool
     * @throws OAuthException
     */
    public function isTimestampNewEnough(LTIRequest $request)
    {
        $now = time();

        if (abs($now - $request->oauth_timestamp) > $this->timestamp_threshold) {
            throw new OAuthException("Expired timestamp, yours $request->oauth_timestamp, ours $now");
        }

        return true;
    }

    /**
     * check that the nonce is not repeated
     * NB, the request has verified that the field is present
     * @param LTIRequest $request
     * @return bool
     * @throws OAuthException
     */
    public function isNonceNew(LTIRequest $request)
    {
        $existing_nonce = UsedNonce::where(['nonce' => $request->oauth_nonce])->first();

        if ($existing_nonce) {
            throw new OAuthException("Nonce already used: $existing_nonce");
        }

        return true;
    }

    /**
     * Verify that the signature is valid
     * @param LTIRequest $request
     * @param ResourceLink $resourceLink
     * @return boolean
     * @throws OAuthException
     */
    public function isSignatureValid(LTIRequest $request, ResourceLink $resourceLink)
    {
        //Will return true if valid; throws exception otherwise
        return $this->signatureMethod->check_signature($request, $resourceLink);
    }

    //Prep for verification


//    public function add_signature_method($signature_method)
//    {
//        $this->signature_methods[$signature_method->get_name()] = $signature_method;
//    }

    // high level functions

//    /**
//     * process a request_token request
//     * returns the request token on success
//     */
//    public function fetch_request_token(&$request)
//    {
//
//        $this->get_version($request);
//
//        $consumer = $this->get_consumer($request);
//
//        // no token required for the initial token request
//        $token = NULL;
//
//        $this->check_signature($request, $consumer, $token);
//
//        // Rev A change
//        $callback = $request->get_parameter('oauth_callback');
//        $new_token = $this->data_store->new_request_token($consumer, $callback);
//
//        return $new_token;
//
//    }
//
//    /**
//     * process an access_token request
//     * returns the access token on success
//     */
//    public function fetch_access_token(&$request)
//    {
//
//        $this->get_version($request);
//
//        $consumer = $this->get_consumer($request);
//
//        // requires authorized request token
//        $token = $this->get_token($request, $consumer, "request");
//
//        $this->check_signature($request, $consumer, $token);
//
//        // Rev A change
//        $verifier = $request->get_parameter('oauth_verifier');
//        $new_token = $this->data_store->new_access_token($token, $consumer, $verifier);
//
//        return $new_token;
//
//    }
//
//    /**
//     * verify an api call, checks all the parameters
//     */
//    public function verify_request(&$request)
//    {
//
//        $this->get_version($request);
//        $consumer = $this->get_consumer($request);
//        $token = $this->get_token($request, $consumer, "access");
//        $this->check_signature($request, $consumer, $token);
//
//        return array($consumer, $token);
//
//    }

    // Internals from here


//    /**
//     * try to find the consumer for the provided request's consumer key
//     */
//    private function get_consumer($request)
//    {
//
//        $consumer_key = $request instanceof OAuthRequest
//            ? $request->get_parameter('oauth_consumer_key') : NULL;
//
//        if (!$consumer_key) {
//            throw new OAuthException('Invalid consumer key');
//        }
//
//        $consumer = $this->data_store->lookup_consumer($consumer_key);
//        if (!$consumer) {
//            throw new OAuthException('Invalid consumer');
//        }
//
//        return $consumer;
//
//    }

//    /**
//     * try to find the token for the provided request's token key
//     */
//    private function get_token($request, $consumer, $token_type = "access")
//    {
//
//        $token_field = $request instanceof OAuthRequest
//            ? $request->get_parameter('oauth_token') : NULL;
//
//        $token = $this->data_store->lookup_token($consumer, $token_type, $token_field);
//        if (!$token) {
//            throw new OAuthException("Invalid $token_type token: $token_field");
//        }
//
//        return $token;
//
//    }


}

