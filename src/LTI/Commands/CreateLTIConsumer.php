<?php
/*
 * Copyright (c) 2021. Adam Swenson
 */

namespace App\LTI\Commands;

use App\LTI\Http\Controllers\LTILaunchController;
use App\LTI\Repositories\ILTIRepository;
use Illuminate\Console\Command;

class CreateLTIConsumer extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new tool consumer';


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lti:create-tool-consumer';

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

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('What do you want to call the new LTIConsumer?');

        $ltiConsumer = $this->ltiRepo->createLTIConsumer($name);

//        $ltiConsumer->save();

        $this->line("Created : {$ltiConsumer->name}");
        $this->line("Consumer Key : {$ltiConsumer->consumer_key}");
        $this->line("Secret Key : {$ltiConsumer->secret_key}");
        $this->line("Launch URL : " . LTILaunchController::getLaunchUrl());

//        $this->table(['name', 'consumer_key', 'secret_key'], $ltiConsumer->toArray());

        return 0;

    }
}
