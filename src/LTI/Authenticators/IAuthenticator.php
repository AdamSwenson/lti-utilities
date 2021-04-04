<?php


namespace App\LTI\Authenticators;

use App\LTI\Http\Requests\LTIRequest;
use App\LTI\Models\ResourceLink;

/**
 * Interface IAuthenticator
 * Interface for everything returned by the authenticator factory
 * @package App\LTI\Authenticators
 */
interface IAuthenticator
{

    /**
     * Handles the relevant authentication
     *
     * @param LTIRequest $request
     * @param ResourceLink $resourceLink
     * @return mixed
     */
    public function authenticate(LTIRequest $request, ResourceLink $resourceLink);


}
