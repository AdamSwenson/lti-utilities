<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace RobertBoes\LaravelLti\Commands;

use App\Repositories\LTI\ILTIRepository;
use Illuminate\Console\Command;
use IMSGlobal\LTI\ToolProvider\ToolConsumer;
use RobertBoes\LaravelLti\LTI;

/**
 * Class CreateToolConsumerCommand
 * @deprecated
 * @package RobertBoes\LaravelLti\Commands
 */
class CreateToolConsumerCommand extends Command
{
    protected $name = 'lti:create-tool-consumer';

    protected $description = 'Creates a new tool consumer';

    /**
     * @var \RobertBoes\LaravelLti\LTI
     */
    protected $lti;

    protected $toolConsumer;
    /**
     * @var ILTIRepository|mixed
     */
    public $ltiRepo;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ltiRepo = app()->make(ILTIRepository::class);

//        $this->lti = new LTI();
//        $this->toolConsumer = $this->lti->toolConsumer();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ltiConsumer = $this->ltiRepo->createLTIConsumer();

        $ltiConsumer->consumer_key = $this->ask('New ToolConsumer key');

        $ltiConsumer->name = $this->ask('ToolConsumer Name');

        $secret = $this->ask('ToolConsumer Secret');

        $this->toolConsumer->secret = $secret;

        $asking = true;

        $this->info('Add optional parameters, enter "exit" to stop and save the new ToolConsumer');
        while($asking) {
            $property = $this->ask('Set property');
            if($property === 'exit') {
                $asking = false;
            }
            else {
                $value = $this->ask('Value of '. $property);
//                $this->toolConsumer->{$property} = $value;
            }
        }

        $ltiConsumer->save();
        return $ltiConsumer;

//        $this->toolConsumer->save();
//        return;
    }
}
