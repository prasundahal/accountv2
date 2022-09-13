<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Log;
use App\Models\Form;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\customMail;
use App\Mail\reportMail;

use App\Models\FormGame;
use App\Models\FormNumber;
use App\Models\Account;
use App\Models\History;
use App\Models\CashApp;
use App\Models\CashAppForm;
use App\Models\FormTip;
use App\Models\FormRefer;
use App\Models\FormBalance;
use App\Models\FormRedeem;
use App\Models\GeneralSetting;

class ColabUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colabUpdate:cron';

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
//         Log::channel('cronLog')->info('Cron Test');
// return;
        Log::channel('cronLog')->info('Reached command ColabUpdate');
        // whereDate('intervals', Carbon::today())->
        $forms = Form::whereDate('intervals', Carbon::today())->get();
        $formData = $forms;
        $settings = GeneralSetting::first();
        if(count($forms)>0){
            foreach($forms as $a => $form){                
                $interval = Carbon::today();
                $daysToAdd = 30;
                $interval = $interval->addDays($daysToAdd);
                $final = date($interval);
                
                $form = Form::find($form->id);
                
                $count = $form->count;
                $count = $count + '1';
                
                $form->count = $count;
                $form->intervals = $interval;
                $form->save();
                Log::channel('cronLog')->info('Month increased for :'.$form['full_name'].' to '.$interval);
            }
            $data = [
                'forms' => $formData,
                'theme' => $settings->theme
            ];
            try {                
                $settings = GeneralSetting::first()->toArray();
                if(!empty($settings['bonus_report_emails'])){
                    $emails = explode(',',$settings['bonus_report_emails']);
                    // Mail::to('joshibipin2052@gmail.com')->send(new customMail(json_encode($data)));

                    foreach($emails as $a){
                        Mail::to($a)->send(new customMail(json_encode($data)));
                        Log::channel('cronLog')->info("Colab Report Mail sent successfully to ".$a);
                    }
                }
            } catch (\Exception $ex) {
                Log::channel('cronLog')->info($ex->getMessage());
            }
        }else{
            
            Log::channel('cronLog')->info('ColabUpdate : Form List Empty');
        }
            
    }
    }

