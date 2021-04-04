<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Repositories;


use App\LTI\Http\Requests\LTIRequest;
use App\Models\Activity;
use App\LTI\Models\LTIConsumer;
use App\LTI\Models\ResourceLink;

/**
 * Class LTIRepository
 *
 * Utilities and access to LTI credentials and
 * settings
 *
 * @package App\Repositories
 */
interface ILTIRepository
{
    /**
     * Creates the credentials and database entry for a new
     * canvas/other lti instance  which will connect to the app
     *
     * @param $name
     * @return LTIConsumer
     */
    public function createLTIConsumer(string $name);

    /**
     * When we get the resource link for our new assignment,
     * we add it to the database.
     *
     * @param LTIConsumer $consumer
     * @param Activity $activity
     * @param $resourceLinkId
     * @param null $description
     * @return ResourceLink
     */
    public function createResourceLinkEntry(LTIConsumer $consumer, Activity $activity, $resourceLinkId, $description = null);

    /**
     * When there is an LTI launch request, this handles
     * getting the resource link object.
     *
     * Since each new assignment in Canvas will have a unique
     * resource link id, we will have to catch the incoming id
     * the first time we see the meeting id in a request. Thereafter,
     * we can just look it up.
     *
     * @param LTIRequest $request
     * @param Activity $activity
     * @return ResourceLink
     */
    public function getResourceLinkFromRequest(LTIRequest $request, Activity $activity);

    /**
     * Handles, ahem, the LTI aspects of the meeting launch request.
     * That involves:
     *      - Performing the OAuth authentication on the LTI request
     *      - Looking up or creating a resource link for the meeting.
     *
     *
     * @param LTIRequest $request
     * @param Activity $activity
     * @throws \App\LTI\Exceptions\InvalidLTILogin
     * @throws \App\LTI\Exceptions\OAuthException
     */
    public function handleActivityLaunchRequest(LTIRequest $request, Activity $activity);
}
