<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_links', function (Blueprint $table) {
            /**
             * this is the db id used for relationships
             */
            $table->id();

            /**
             * This will be used unique id referencing the link, or "placement", of the app in the consumer.
             * If an app was added twice to the same class, each placement would send a different id, and should be considered a unique "launch". For example, if the provider were a chat room app, then each resource_link_id would be a separate room.
             *
             * This will be provided by the LMS, thus it is separate from the db id since we may need to create
             * the resource link object before knowing what the LMS calls it
             */
            $table->text('resource_link_id')->nullable();

            $table->text('description')->nullable();

            /** Foreign key for associated activity */
            $table->integer('activity_id');

            //For oauth access
            $table->integer('lti_consumer_id');


            $table->timestamps();

//            $table->string('consumer_key')->nullable();
//            $table->string('secret_key')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_links');
    }
}
