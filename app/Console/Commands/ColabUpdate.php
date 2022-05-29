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
         $myfile = fopen("/home3/noorgame/mail_log.txt", "a");
        $contents=date("Y-m-d H:i:sa")." triggered from function \n -------------------------------- \n";
         $txt = $contents;
          fwrite($myfile, $txt);
         fclose($myfile);
        Log::channel('cronLog')->info("Reached to command.");
        $forms = Form::whereDate('intervals', Carbon::today())->get();
        $formData = $forms;
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
                // Log::info('Month increased for :'.$form['full_name'].' to '.$interval);
    
            }
            //write lo

            //Storage::prepend('cron_log.txt', $contents);
            
           
            
            try {
                // Mail::to('joshibipin2052@gmail.com')->send(new customMail(json_encode($formData)));
                //       Mail::to('russelcraigspencer@gmail.com')->send(new customMail(json_encode($formData)));
                //     Mail::to('prasundahal@gmail.com')->send(new customMail(json_encode($formData)));
                //     Mail::to('slimnoor96@gmail.com')->send(new customMail(json_encode($formData)));
                // $date = date();
                // $date->setTimezone(new DateTimeZone('America/New_York'));
                // $datecurrent=$date->format('H');
                $settings = GeneralSetting::first()->toArray();
                if(!empty($settings['bonus_report_emails'])){
                    $emails = explode(',',$settings['bonus_report_emails']);
    
                    foreach($emails as $a){
                        Mail::to($a)->send(new customMail(json_encode($formData)));
                        Log::channel('cronLog')->info("Colab Report Mail sent successfully to ".$a);
                    }
                }
                // if ($datecurrent=='00') {
                    // echo $date->format('Y-m-d H:i:s'); 
                    // 2012-07-15 05:00:00 
                    // Mail::to('joshibipin2052@gmail.com')->send(new customMail(json_encode($formData)));
                    // Mail::to('russelcraigspencer@gmail.com')->send(new customMail(json_encode($formData)));
                    // Mail::to('prasundahal@gmail.com')->send(new customMail(json_encode($formData)));
                    // Mail::to('slimnoor96@gmail.com')->send(new customMail(json_encode($formData)));
                    // Log::channel('cronLog')->info("Mail sent successfully to russelcraigspencer@gmail.com, slimnoor96@gmail.com");
                // }
            } catch (\Exception $ex) {
                Log::channel('cronLog')->info($ex->getMessage());
            }
            
            //   Mail::to('joshibipin2052@gmail.com')->send(new customMail(json_encode($formData)));
        }
            
    }
    }

