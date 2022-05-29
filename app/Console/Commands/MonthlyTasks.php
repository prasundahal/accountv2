<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\Form;


class MonthlyTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        Log::channel('dailyReport')->info("Monthly Task : Reset balance of form table");
        try {
            Form::query()->update([
                'balance' => 0,
                'token' => null
            ]);
        } catch (Exception $ex) {
                    Log::channel('dailyReport')->info($ex->getMessage());
        }
        // return 0;
    }
}
