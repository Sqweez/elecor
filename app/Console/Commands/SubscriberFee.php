<?php

namespace App\Console\Commands;

use App\Client;
use Illuminate\Console\Command;

class SubscriberFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriber:fee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take a subscriber fee';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Client::all()->first()->delete();
    }
}
