<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Models;

use App\Models\Activity;
use App\LTI\Models\LTIConsumer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceLink extends Model
{
    use HasFactory;

    protected $fillable = [
        /**
         * The internal-to-pwb object which ties everything together.
         */
        'activity_id',

        'description',

        /**
         * Link to the credentials used for OAuth access.
         */
        'lti_consumer_id',

        /**
         * This will be used unique id referencing the link, or "placement", of the app in the consumer.
         * If an app was added twice to the same class, each placement would send a different id, and should be considered a unique "launch". For example, if the provider were a chat room app, then each resource_link_id would be a separate room.
         *
         * This will be provided by the LMS, thus it is separate from the db id since we may need to create
         * the resource link object before knowing what the LMS calls it.
         *
         */
        'resource_link_id'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity(){
        return $this->belongsTo(Activity::class);
    }
    /**
     * The consuming application which contains the resource link placement
     * (i.e., canvas)
     */
    public function ltiConsumer(){
        return $this->belongsTo(LTIConsumer::class);
    }

}
