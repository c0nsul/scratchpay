<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Superbalist\LaravelPubSub\PubSubConnectionFactory;
use Illuminate\Support\Facades\Log;

class BuisnessDaysSubscriber extends Command
{
    /**
     * The name and signature of the subscriber command.
     *
     * @var string
     */
    protected $signature = 'BankWire';

    /**
     * The subscriber description.
     *
     * @var string
     */
    protected $description = 'PubSub subscriber for bank data ';


    /**
     * @var
     */
    private $pubsubsFactory;


    /**
     * Create a new command instance.
     *
     * @param PubSubAdapterInterface $pubsub
     */

    public function __construct(PubSubConnectionFactory $pubsubsFactory)
    {

        parent::__construct();
        $connectionName = config('pubsub.default');
        $connection = config("pubsub.connections.$connectionName");
        $this->subscriber = $pubsubsFactory->make($connection['driver'], $connection);

    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->subscriber->subscribe('BankWire:businessDates', function ($message) {
            if (is_array($message) && count($message) == 2  ){

                try {
                    $request = new \Illuminate\Http\Request();
                    $request->request->add(['json' => json_encode($message)]);
                    $buisnessDaysController = new \App\Http\Controllers\BuisnessDaysController();
                    $buisnessDaysController->inputData($request);
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        });
    }
}