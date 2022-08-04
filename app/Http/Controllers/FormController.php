<?php

namespace App\Http\Controllers;
session_start();

use App\Mail\monthlyMail;
use App\Models\Form;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Facades\Mail;
// use App\Mail\UserNoticMail;
use App\Mail\UserNoticMail as UserNoticMail;
use App\Mail\NoticeUserMail as NoticeUserMail;

use App\Models\FormGame;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSetting;
use App\Models\History;
use Illuminate\Support\Facades\Log;


class FormController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['except' => [
            'store','checkCaptcha','go'
        ]]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function test(){
            $limit_amount = 10;
            $history = History::with('account')->with('form')
                                ->whereHas('form')
                                // ->whereBetween('created_at', [Carbon::now()->subMinutes(1440), now()])
                                ->orderBy('id', 'desc')
                                ->get()
                                ->toArray();

            $final = [];
            $forms = [];

            if (!empty($history))
            {
                foreach ($history as $a => $b)
                {
                    $totals = [
                        'tip' => 0, 
                        'load' => 0, 
                        'redeem' => 0, 
                        'refer' => 0, 
                        'cashAppLoad' => 0
                    ];

                    $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                    if (!empty($form_game))
                    {
                        $form = Form::where('id', $b['form_id'])->first();
                        if (!empty($form))
                        {
                            $form_game->toArray();
                            $form->toArray();

                            if (!(isset($final[$b['form_id']])))
                            {
                                $final[$b['form_id']] = [];
                            }
                            $final[$b['form_id']]['form_id'] = $b['form_id'];
                            $final[$b['form_id']]['spinner_key'] = $form['token'];
                            $final[$b['form_id']]['full_name'] = $form['full_name'];
                            $final[$b['form_id']]['number'] = $form['number'];
                            $final[$b['form_id']]['email'] = $form['email'];
                            $final[$b['form_id']]['facebook_name'] = $form['facebook_name'];
                        }

                        // $b['form_game'] = $form_game;
                        if (isset($final[$b['form_id']]['totals']))
                        {
                            $totals['tip'] = $final[$b['form_id']]['totals']['tip'];
                            $totals['load'] = $final[$b['form_id']]['totals']['load'];
                            $totals['redeem'] = $final[$b['form_id']]['totals']['redeem'];
                            $totals['refer'] = $final[$b['form_id']]['totals']['refer'];
                            $totals['cashAppLoad'] = $final[$b['form_id']]['totals']['cashAppLoad'];
                        }

                        ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                        ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                        ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                        ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                        ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);
                        $final[$b['form_id']]['totals'] = $totals;
                        
                    }
                }
            }
            $limit = 0;
            $final_redeem = [];
            $final_tip = [];
            if(!empty($final)){
                foreach ($final as $a => $b){
                    if($b['totals']['redeem']  >= $limit_amount){
                        array_push($final_redeem,$b);
                    }
                    if($b['totals']['tip']  >= $limit_amount){
                        array_push($final_tip,$b);
                    }
                }
                // dd($final_redeem,$final_tip);
                if(empty($final_redeem)){
                    Log::channel('cronLog')->info('Empty Data Today For Mail of Redeem');
                }else{
                    $details = [
                        'subject' => 'Total Players this month who redeemed more than '.$limit_amount,
                        'type' => 'redeem',
                        'data' => $final_redeem
                    ];                    
                    // Mail::to('joshibipin2052@gmail.com')->send(new monthlyMail(json_encode($details)));
                }
                
                if(empty($final_tip)){
                    Log::channel('cronLog')->info('Empty Data Today For Mail of Tip');
                }else{
                    $details = [
                        'subject' => 'Total Players this month who tipped more than '.$limit_amount,
                        'type' => 'tip',
                        'data' => $final_redeem
                    ];                    
                    Mail::to('joshibipin2052@gmail.com')->send(new monthlyMail(json_encode($details)));
                    
                }
            }else{
                //empty all data
                echo 'a';
                Log::channel('cronLog')->info('Empty Data Today For Mail of Redeem & Tip');
            }
            // dd('l');
               

    }
    
    public function index()
    {
      //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('Sorry');
        return view('welcome');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function checkCaptcha(Request $request){
         $entered_captcha=strtoupper($request->captcha);
        $generated_captcha=strtoupper($_SESSION['captcha_token']);
        if($entered_captcha!=$generated_captcha) {
            return Response::json('false');
        }
        return Response::json('true');
     }
    public function store(Request $request, Form $form)
    {
        
        $settings = GeneralSetting::first();
        
        $request->validate([
            'full_name' => 'required|min:3|max:20',
            'facebook_name' => 'required|min:7|unique:forms,facebook_name'.$form->id,
            'game_id' => 'required|min:7|unique:forms,game_id'.$form->id,
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number'.$form->id,
            'mail' => 'required',
            
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|min:6|unique:forms,email'.$form->id,
            'account' => 'required',
         
        ]);

        $interval = Carbon::today();
        $daysToAdd = 30;
        $interval = $interval->addDays($daysToAdd);
        $final = date($interval);
        $count = 0;      
     
        
        
           
        $formdata = array(
            'full_name'=>$request->full_name,
            'number'=>   $request->number,
            'email' =>   $request->email,
            'mail' =>   $request->mail,
            'r_id'  =>   $request->r_id,
            'game_id' => $request->game_id,
            'facebook_name'=>$request->facebook_name,
            'intervals'=>$final,
            'count'=> $count
        );
        $form =  Form::create($formdata);
        FormGame::create([
            'form_id' => $form->id,
            'account_id' => $request->account,
            'game_id' => $request->game_id,

        ]);


        $boyname  = $request->full_name;
      
        $mail_text = $settings->mail_text;
        $sms_text = $settings->sms_text;
        $game_name = strtoupper(($settings->theme == 'default')?'noor':$settings->theme);
        $sendtext = 'Hello Admin, '.$boyname . ' ' .$mail_text.' ';
        $sendtextuser = 'Congratulations, '.$boyname . '!!! ' . 'Welcome to '.$game_name.' Games Club. '.$sms_text;
        
        $details = $sendtext;

        if($settings->registration_email == 1){
            Mail::to($request->email)->send(new NoticeUserMail(($sendtextuser)));
        }
        if($settings->registration_sms == 1){
            $key = (string) $settings['api_key'];
            $secret = (string) $settings['api_secret'];
            $basic  = new \Vonage\Client\Credentials\Basic($key, $secret);
            $client = new \Vonage\Client($basic);

            $message = $client->message()->send([
                // $request->number
                'to' => $request->number,
                'from' => '18337222376',
                'text' => $sendtext
            ]);
        }
        try
        {
             $details1 = [
                'text' => $details,
                'theme' => ($settings->theme)
            ];
            Mail::to('riteshnoor69@gmail.com')->send(new UserNoticMail(($details1)));
            // $job = (new \App\Jobs\NewRegistrationAlert($details))
            //     ->delay(now()->addSeconds(2)); 
            // dispatch($job);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            Log::channel('cronLog')->info('Error sending new registration mail to admin '.$bug);
        }

        // Mail::to('prasundahal@gmail.com')->send(new UserNoticMail(json_encode($details)));

        switch($settings->theme) {
            case('anna'):
                return view('frontend.'.$settings->theme.'.success')->with('success', 'You should be receiving the confirmation text on the number that you registered. Stay connected with Noor Games for much exciting Bonus & Reward.Happy Playing');;
                break;
    
            default:
                return view('frontend.'.$settings->theme.'.success')->with('success', 'You should be receiving the confirmation text on the number that you registered. Stay connected with Noor Games for much exciting Bonus & Reward.Happy Playing');;
        }
        // Form::create($formdata);      
        // $number  =  '1'.$request->number;
        // $basic  = new \Vonage\Client\Credentials\Basic("e20bd554", "M5arJoXIrJ8Kat1r");
        // $client = new \Vonage\Client($basic);
              
        // $message = $client->message()->send([
        //     'to' => $number,
        //     'from' => '18337222376',
        //     'text' => $sendtextuser
        // ]);        
       
        
    }
    
    
    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function show(Form $form)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    // public function edit(Form $form)
    // {

    //     return view('forms.edit', compact('form'));
    // }
      public function edit($id)
    {
        $form = Form::where('id',$id)->first();

        return view('forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $form = Form::find($id);
        $this->validate($request, [
            'full_name' => 'required',
           
            'number' => 'required',
            
        ]);
        $form->full_name = $request->full_name;
        $form->email = $request->email;
        $form->intervals = $request->intervals;
        
        $form->number = $request->number;
          $form->count = $request->count;
        $form->note = $request->note;
        $form->facebook_name = $request->facebook_name;
        $form->game_id = $request->game_id;
        $form->save();
        return redirect(route('home'))->with('message', " Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
        $form = Form::where('id',$id)->delete();
    //   Form::find($id)->delete($id);
  
   return redirect(route('home'))->with('message', " Deleted Successfully");
    }
    
      public function saveNote(Request $request)
    {
        $formdata = array(
            'note'       =>   isset($request->note)?($request->note):null,
        );
        $sql = Form::find($request->cid);  
        $sql->note = isset($request->note)?($request->note):null;
        if(!$sql->save()){
            return Response::json(['error' => $sql],404);
        }
            return Response::json('Note Saved');
    }
    
    public function restorePlayers($id)
    {
        $form = Form::withTrashed()->find($id);
        if(!is_null($form))
        {
            $form->restore();
        }
          return redirect(route('home'))->with('message', " Player restored Successfully");
    }
    
     public function forceDeletePlayers($id)
    {
        $form = Form::withTrashed()->find($id);
        if(!is_null($form))
        {
            $form->forceDelete();
        }
          return redirect(route('home'))->with('message', " Player deleted Successfully");
    }
 
}
