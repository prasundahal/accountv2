<?php

namespace App\Console\Commands;

use App\Models\Form;
use App\Models\GeneralSetting;
use App\Models\History;
use App\Models\SpinnerWinner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SpinnerWinnerCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SpinnerWinnerCron:cron';

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
        Log::channel('spinnerWinnerCron')->info('Reached command SpinnerWinnerCron');

        
        $settings = GeneralSetting::first();
        
        $year = date('Y');
        $month = date('m');
        if($month != 1){
            $month = $month - 1;
        }else{
            $month = 12;
        }
        if($month < 10){
            $month = '0'.$month;
        }
        $filter_start = $year.'-'.$month.'-01';
        $filter_end = date("Y-m-t", strtotime($year.'-'.$month.'-01'));
        $winners_list = SpinnerWinner::whereBetween('created_at',[date($filter_start),date($filter_end)])->count();
        // dd($winners_list,$filter_start);
        if($winners_list <= 0){
            
            $year = date('Y');
            $month = date('m');
            if($month != 1){
                $month = $month - 1;
            }else{
                $month = 12;
            }
            if($month <10){
                $month = '0'.$month;
            }
            $filter_start = $year.'-'.$month.'-01';
            $filter_end = date("Y-m-t", strtotime($year.'-'.$month.'-01'));
            $compare_amount = $settings->limit_amount;
            $historys = History::where('type', 'load')
                                ->whereBetween('created_at',[date($filter_start),date($filter_end)])
                                ->select([DB::raw("SUM(amount_loaded) as total") , 'form_id as form_id'])
                                ->groupBy('form_id')
                                ->with('form')
                                ->whereHas('form')
                                ->get();
                                
            if (($historys->count()) > 0)
            {
                $historys = $historys->toArray();
                $final = [
                    'players_list' => [],
                    'winner_info' => []
                ];
                foreach ($historys as $a => $b)
                {
                    // dd($b);
                    $explode_name = explode(' ',$b['form']['full_name']);

                    if ($b['total'] >= $compare_amount)
                    {                        
                        $z =[
                            'player_name' => $b['form']['full_name'],
                            'player_id' => $b['form_id']
                        ];
                        array_push($final['players_list'], $z);
                    }
                }
                
// dd($historys,$final);
                $shuffle = array_rand($final['players_list']);
                
                $f1 = Form::where('id',$final['players_list'][$shuffle]['player_id'])->first();
                $winner = SpinnerWinner::create([
                    'form_id' => $final['players_list'][$shuffle]['player_id'],
                    'full_name' => $f1->full_name,
                    'created_at' => $filter_start
                ]);
// dd($winner);
                
                // Log::channel('spinnerWinnerCron')->info($historys);
                Log::channel('spinnerWinnerCron')->info('Winner Set for this month is '.$winner['full_name']);
            }else{
                // dd('2');
                Log::channel('spinnerWinnerCron')->info('No Players in this month.');
            }
            
        }else{
            // dd('here');
            $winner = SpinnerWinner::whereBetween('created_at',[date($filter_start),date($filter_end)])->first();
            Log::channel('spinnerWinnerCron')->info('Winner already set for this month, '.$winner->full_name);
        }
    }
}
