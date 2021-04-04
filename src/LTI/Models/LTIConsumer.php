<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LTIConsumer
 * These are apps which connect to PWB.
 * For now, there will only be one
 * When canvas is configured to use the external app
 * this is the model which will hold the keys
 * @package App
 */
class LTIConsumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'consumer_key',
        'secret_key'
    ];

    //
    public function resourceLink()
    {
        return $this->hasMany(ResourceLink::class);
    }
}
