<?php
namespace App\Http\Controllers;

use App\Mail\crossedPlayers;
use App\Mail\spinnerBulkMail;
use App\Models\Form;
use App\Models\FormGame;
use App\Models\FormNumber;
use App\Models\Account;
use App\Models\History;
use App\Models\CashApp;
use App\Models\CashAppForm;
use Illuminate\Support\Facades\Log;
use App\Models\FormTip;
use App\Models\FormRefer;
use App\Models\FormBalance;
use App\Models\FormRedeem;
use App\Mail\reportMail as reportMail;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Datatables;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\customMail;
use App\Models\LoginLog;
use App\Models\ActivityStatus;
use App\Models\GeneralSetting;
use App\Models\SpinnerWinner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NewHomeController extends Controller
{
    public $color = 'purple';
    public $limit_amount = 0;
    public $spinner_message_monthly = 0;
    public $spinner_message = 0;

    public function __construct()
    {
        $settings = GeneralSetting::first()->toArray();
        $this->limit_amount = $settings['limit_amount'];
        $this->spinner_message_monthly = $settings['spinner_message_monthly'];
        $this->spinner_message = $settings['spinner_message'];
        
        $this->middleware('auth', ['except' => ['userSpinner']]);
        $color = DB::table('sidebar')->where('id', 1)
            ->first('color');
        view()->share('color', $color);
        view()->share('limit_amount', $this->limit_amount);
        view()->share('spinner_message_monthly', $this->spinner_message_monthly);
        view()->share('spinner_message', $this->spinner_message);
    }

    public function profile()
    {
        return view('newLayout.profile');
    }
     public function loginLogs()
    {
        $alogs = LoginLog::with('user')->whereHas('user')
            ->orderBy('login_time', 'desc')
            ->get();
        $logs = array();
        $interval = $intervaly = $intervalm = $intervald = $intervalh = $intervali = $intervals = 0;
        $all_months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
        foreach($alogs as $log) {
            $date = \Carbon\Carbon::parse($log->login_time);
            $logdate = $date->format('m');
            if(!isset($logs[$log->user_id][$logdate])){
                $logs[$log->user_id][$logdate] = [
                    'id' => $log->id,
                    'date' => $date->format('Y-m-d'),
                    'user_name' => $log->user->name,
                    'last_login_time' => $log->login_time,
                    'total_login_time' => [
                        'd' => 0,
                        'h' => 0,
                        'i' => 0,
                        's' => 0,
                    ],
                    'interval' => 0
                ];
            }else{
                $logs[$log->user_id][$logdate]['first_login_time'] = $log->login_time;
            }
            if($log->logout_time){
                $datetime1 = strtotime($log->logout_time);
                $datetime2 = strtotime($log->login_time);
                $logs[$log->user_id][$logdate]['interval'] += $datetime1 - $datetime2;
                $interval = $logs[$log->user_id][$logdate]['interval'];
                $secondsInAMinute = 60;
                $secondsInAnHour  = 60 * $secondsInAMinute;
                $secondsInADay    = 24 * $secondsInAnHour;

                // extract days
                $days = floor($interval / $secondsInADay);

                // extract hours
                $hourSeconds = $interval % $secondsInADay;
                $hours = floor($hourSeconds / $secondsInAnHour);

                // extract minutes
                $minuteSeconds = $hourSeconds % $secondsInAnHour;
                $minutes = floor($minuteSeconds / $secondsInAMinute);

                // extract the remaining seconds
                $remainingSeconds = $minuteSeconds % $secondsInAMinute;
                $seconds = ceil($remainingSeconds);

                $logs[$log->user_id][$logdate]['total_login_time']['d'] = $days;
                $logs[$log->user_id][$logdate]['total_login_time']['h'] = $hours;
                $logs[$log->user_id][$logdate]['total_login_time']['i'] = $minutes;
                $logs[$log->user_id][$logdate]['total_login_time']['s'] = $seconds;
            }
        }

        // dd($logs);
        return view('newLayout.loginLogs', compact('logs', 'all_months'));
    }
     public function thisMonthLogs(Request $request) {
        $alogs = LoginLog::with('user')->whereHas('user')
            ->orderBy('login_time', 'desc')
            ->get();
        $logs = array();
        $interval = $intervaly = $intervalm = $intervald = $intervalh = $intervali = $intervals = 0;
        foreach($alogs as $log) {
            $date = \Carbon\Carbon::parse($log->login_time);
            $checkdate = $date->format('m');
            if($checkdate == $request->month){
                $logdate = $date->format('Ymd');
                if(!isset($logs[$logdate])){
                    $logs[$logdate] = [
                        'id' => $log->id,
                        'date' => $date->format('Y-m-d'),
                        'time' => $date->format('h:i:s'),
                        'user_name' => $log->user->name,
                        'last_login_time' => $log->login_time,
                        'total_login_time' => [
                            'd' => 0,
                            'h' => 0,
                            'i' => 0,
                            's' => 0,
                        ],
                        'interval' => 0
                    ];
                }else{
                    $logs[$logdate]['first_login_time'] = $log->login_time;
                }
                if($log->logout_time){
                    $datetime1 = strtotime($log->logout_time);
                    $datetime2 = strtotime($log->login_time);
                    $logs[$logdate]['interval'] += $datetime1 - $datetime2;
                    $interval = $logs[$logdate]['interval'];
                    $secondsInAMinute = 60;
                    $secondsInAnHour  = 60 * $secondsInAMinute;
                    $secondsInADay    = 24 * $secondsInAnHour;

                    // extract days
                    $days = floor($interval / $secondsInADay);

                    // extract hours
                    $hourSeconds = $interval % $secondsInADay;
                    $hours = floor($hourSeconds / $secondsInAnHour);

                    // extract minutes
                    $minuteSeconds = $hourSeconds % $secondsInAnHour;
                    $minutes = floor($minuteSeconds / $secondsInAMinute);

                    // extract the remaining seconds
                    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
                    $seconds = ceil($remainingSeconds);

                    $logs[$logdate]['total_login_time']['d'] = $days;
                    $logs[$logdate]['total_login_time']['h'] = $hours;
                    $logs[$logdate]['total_login_time']['i'] = $minutes;
                    $logs[$logdate]['total_login_time']['s'] = $seconds;
                }
            }
        }

        return Response::json($logs);
    }
    public function dashboard()
    {
        // $form = Form::where('id',5)->update(['balance' => 1,'token' => $token_id]);
        // Mail::to('joshibipin2052@gmail.com')->send(new crossedPlayers(json_encode($form)));
        // $form = Form::where('id',343)->first()->toArray();
        // dd(Mail::to('joshibipin2052@gmail.com')->send(new crossedPlayers(json_encode($form))));
        $total = self::totals();
        $formCount = Form::count();
        $games = Account::where('status', 'active')->get()
            ->toArray();
        // dd($total);
        return view('newLayout.dashboard', compact('total', 'formCount', 'games'));
        // return view('new.dashboard',compact('total'));
        
    }

    public function colab()
    {
        $title = 'All Collabration';
        $number = FormNumber::orderBy('id', 'asc')->get();
        $total = FormNumber::count();
        return view('newLayout.colab', compact('number', 'total', 'title'));
    }

public function tableop()
{
    $toSearch = isset($_GET['search']) ? $_GET['search'] : false;
    $activeGame = isset($_GET['game']) ? $_GET['game'] : '';
    
     if (empty($activeGame))
            {
                $activeGameDefault = Account::first()->toArray();
                $activeGame = $activeGameDefault['title'];
            }
            if (empty($activeCashApp))
            {
                $cash_app_default = CashApp::first()->toArray();
                $activeCashApp = $cash_app_default['title'];
            }
            $cashApp = CashApp::where([['status', 'active']])->get()
                ->toArray();

            $activeCashApp = CashApp::where([['title', $activeCashApp], ['status', 'active']])->first()
                ->toArray();
    
    $activeGame = Account::where([['title', $activeGame], ['status', 'active']])->with('formGames')
                ->first()
                ->toArray();
    
    // $activeGame = Account::where([['title', $activeGame], ['status', 'active']])->with('formGames')
    //             ->first()   
    //             ->toArray();
                
                
            // dd($activeGame['form_games']);
            
           

            $final = [];
            if (!empty($activeGame['form_games']))
            {
                foreach ($activeGame['form_games'] as $a => $b)
                {
                    $tip = FormTip::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $refer = FormRefer::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $cash = CashAppForm::where('form_id', $b['form']['id'])->where('cash_app_id', $activeCashApp['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $balance = FormBalance::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $redeem = FormRedeem::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $b['cash_app'] = $cash;
                    $b['tip'] = $tip;
                    $b['refer'] = $refer;
                    $b['balance'] = $balance;
                    $b['redeem'] = $redeem;
                    array_push($final, $b);
                }
                $activeGame['form_games'] = $final;
                $responselist=[];
                
                foreach ($activeGame['form_games'] as $formgame=>$value) {
                    $userArray=$value['form'];
                    $name=$userArray['facebook_name'];
                    
                    $name=strtoupper($name);
                    $toSearch=strtoupper($toSearch);
                    
                    if (strpos($name, $toSearch)) {
                        array_push($responselist,$value);
                    }
                }
                
                $activeGame['form_games']=$responselist;
               //print_r($activeGame['form_games']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($activeGame['form_games']);
                die();
            }
           
    
}


    public function editColab($id)
    {
        $form = FormNumber::where('id', $id)->first();
        // dd($form);
        return view('newLayout.colabEdit', compact('form'));
    }

    public function destroyColab($id)
    {
        $form = FormNumber::where('id', $id)->delete();
        //   Form::find($id)->delete($id);
        return redirect(route('dashboard.colab'))
            ->with('message', " Deleted Successfully");
    }

    public function updateColab(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number',
        //     'extra_2' => 'required'
        // ]);
        // $request->validate([
        // 'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number'
        // ]);
        // if ($validator->fails()) {
        //     return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        // }
        $formdata = array(
            'note' => isset($request->note) ? ($request->note) : null,
            'extra_2' => isset($request->extra_2) ? ($request->extra_2) : null,
            'phone_number' => isset($request->phone_number) ? ($request->phone_number) : null

        );
        $sql = FormNumber::find($request->id);
        $sql->note = isset($request->note) ? ($request->note) : null;
        $sql->extra_2 = isset($request->extra_2) ? ($request->extra_2) : null;
        $sql->phone_number = isset($request->phone_number) ? ($request->phone_number) : null;
        if (!$sql->save())
        {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $sql);
        }

        // $sendtext = $request->phone_number . ' ' . 'has joined for vaccency .
        // Yayyy ';
        // $basic  = new \Vonage\Client\Credentials\Basic("e20bd554", "M5arJoXIrJ8Kat1r");
        // $client = new \Vonage\Client($basic);
        // $message = $client->message()->send([
        // 'to' => '19292684435',
        // 'from' => '18337222376',
        // 'text' => $sendtext
        // ]);
        return redirect(route('dashboard.colab'))->with('message', " Updated Successfully");
    }

    public function gamers()
    {
        // orderBy('id','desc')->
        $total = Form::count();
        $forms = Form::get();
        $trashed = Form::onlyTrashed()->orderBy('id', 'desc')
            ->get()
            ->toArray();
        // dd($trashed);
        return view('newLayout.gamers', compact('total', 'forms', 'trashed'));
    }
    public function gamerDestroy($id)
    {
        try
        {
            $form = Form::findOrFail($id);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect(route('gamers'))
                ->with('error', $bug);
        }
        if ($form->delete() == true)
        {
            $delete_form_balance = FormBalance::where('form_id', $id)->delete();
            return Response::json('true');
            // return redirect(route('gamers'))->with('success', " Gamer Deleted Succesfully");
            
        }
        else
        {
            return Response::json(['error' => $bug], 404);
            // return redirect(route('gamers'))->with('error', $form);
            
        }
    }
    public function gamerRestore($id)
    {
        // dd($id);
        try
        {
            $form = Form::onlyTrashed()->where('id', $id)->restore();
            return redirect(route('gamers'))
                ->with('success', " Gamer Restored Succesfully");
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect(route('gamers'))
                ->with('error', $bug);
        }
        // if($form->delete() == true){
        //     return Response::json('true');
        //     // return redirect(route('gamers'))->with('success', " Gamer Deleted Succesfully");
        // }else{
        //     return Response::json(['error' => $bug],404);
        //     // return redirect(route('gamers'))->with('error', $form);
        // }
        
    }

    public function gamerEdit($id)
    {
        $form = Form::where('id', $id)->first();
        $html = view('new.gamerEditModal', compact('form'))->render();
        return Response::json($html);
        // return view('new.gamers-edit', compact('form'));
        
    }

    public function gamerUpdate(Request $request, $id)
    {
        // return Response::json($_POST['data']);
        // return Response::json($request->all);
        $request = $_POST['data'];
        try
        {
            $form = Form::findOrFail($id);

            if (!(isset($request['full_name']) && !empty($request['full_name'])))
            {
                return Response::json(['error' => 'Full Name is Empty'], 404);
            }
            if (!(isset($request['email']) && !empty($request['email'])))
            {
                return Response::json(['error' => 'Email is Empty'], 404);
            }
            if (!(isset($request['number']) && !empty($request['number'])))
            {
                return Response::json(['error' => 'Number is Empty'], 404);
            }

            // $this->validate($request, [
            //     'full_name' => 'required',
            //     'number' => 'required',
            // ]);
            $form->full_name = isset($request['full_name']) ? $request['full_name'] : null;
            $form->email = isset($request['email']) ? $request['email'] : null;
            $form->intervals = isset($request['intervals']) ? $request['intervals'] : null;

            $form->number = isset($request['number']) ? $request['number'] : null;
            $form->count = isset($request['count']) ? $request['count'] : null;
            $form->note = isset($request['note']) ? $request['note'] : null;
            $form->facebook_name = isset($request['facebook_name']) ? $request['facebook_name'] : null;
            $form->game_id = isset($request['game_id']) ? $request['game_id'] : null;
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
            // return redirect(route('gamers'))->with('error', $bug);
            
        }
        if ($form->save() == true)
        {
            return Response::json($form);
            // return redirect(route('gamerEdit',['id' => $request->id]))->with('success', "Updated Successfully");
            
        }
        else
        {
            return Response::json(['error' => $form], 404);
            // return redirect(route('gamerEdit',['id' => $request->id]))->with('error', $form);
            
        }
    }

    public function changeColor(Request $request)
    {
        try
        {
            DB::table('sidebar')->where('id', 1)
                ->update(['color' => $request->cid]);
            return Response::json('Color Changed');
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            // return redirect()->back()->with('error', $bug);
            // return redirect()->back()->withInput()->with('error', $bug);
            return Response::json(['error' => $bug], 404);
        }
    }
    function multi_array_diff($arraya, $arrayb)
    {
        foreach ($arraya as $keya => $valuea)
        {
            if (in_array($valuea, $arrayb))
            {
                unset($arraya[$keya]);
            }
        }
        return $arraya;
    }
    public function inactivePlayers($id)
    {
        // [Carbon::today(),Carbon::today()->subWeek()]
        $days = $id;
        $users = FormGame::select('form_id')->distinct()
            ->get()
            ->toArray();
        // $balance = FormBalance::select('form_id')
        //                         ->where( 'created_at', '>', Carbon::now()->subDays($days))
        //                         ->get()
        //                         ->toArray();
        // dd(Carbon::now()->subDays($days),$users,$balance);
        $balance = FormBalance::select('form_id')->where('created_at', '>', Carbon::now()
            ->subDays($days))
        // ->whereBetween('created_at',['2022-02-9','2022-02-11'])
        ->distinct()
            ->get()
            ->toArray();
        $differenceArray = self::multi_array_diff($users, $balance);
        $array = array_column($differenceArray, 'form_id');
        // print_r(array(implode(',',$array)));
        // $models = Form::findMany([225,232,233]);
        $forms = Form::with('activityStatus')->whereIn('id', $array)->get();
        $activity_status = ActivityStatus::orderBy('status', 'asc')->get();
        return view('newLayout.inactive-player', compact('forms', 'days', 'activity_status'));
    }

    public function userSpinner($token)
    {
        $form_token = Form::where('token',$token)->count();
        if($form_token > 0 ){
            $form_token = Form::where('token',$token)->first('id')->toArray();
        }
        $month = date('m');
        if($month >10){
            $month = '0'.$month;
        }
        $filter_start = '2022-'.$month.'-01';
        $filter_end = date("Y-m-t", strtotime(Carbon::now()));
        $compare_amount = $this->limit_amount;
        $historys = History::where('type', 'load')
            // ->where('created_at', '>', Carbon::now()
            // ->subDays(30))
            ->whereBetween('created_at',[date($filter_start),date($filter_end)])
            ->select([DB::raw("SUM(amount_loaded) as total") , 'form_id as form_id'])
            ->groupBy('form_id')
            ->with('form')
            ->whereHas('form')
            ->get();

        $winners_list = SpinnerWinner::whereBetween('created_at',[date($filter_start),date($filter_end)])->count();

        // $final = [];
        $final = [
            'players_list' => [],
            'winner_info' => []
        ];

        // if (($historys->count()) > 1)
        // {
        //     $historys = $historys->toArray();
        //     foreach ($historys as $a => $b)
        //     {
        //         if ($b['total'] >= $compare_amount)
        //         {
        //             array_push($final, $b);
        //         }
        //     }
        // }
        
        if (($historys->count()) > 1)
        {
            $historys = $historys->toArray();
            foreach ($historys as $a => $b)
            {
                // dd($b);
                $explode_name = explode(' ',$b['form']['full_name']);
                $full_name_encrypt = '';
                foreach($explode_name as $z){
                    $string = substr($z,0,2);
                    $string .= '**** ';
                    $full_name_encrypt .= $string;
                }
                if ($b['total'] >= $compare_amount)
                {
                    if($form_token > 0){
                        if($b['form_id'] == $form_token['id']){
                            $z =[
                                'player_name' => $b['form']['full_name'],
                                'player_id' => $b['form_id']
                            ];
                        }else{
                            $z =[
                                'player_name' => $full_name_encrypt,
                                'player_id' => $b['form_id']
                            ];
                        }
                    }else{
                        $z =[
                            'player_name' => $full_name_encrypt,
                            'player_id' => $b['form_id']
                        ];
                    }
                    array_push($final['players_list'], $z);
                }
            }
            // if(){
                
            // }
            if(!empty($final['players_list'])){
                    
                $shuffle = array_rand($final['players_list']);

                if($winners_list <= 0){
                    $f1 = Form::where('id',$final['players_list'][$shuffle]['player_id'])->first();
                    $winner = SpinnerWinner::create([
                        'form_id' => $final['players_list'][$shuffle]['player_id'],
                        'full_name' => $f1->full_name
                    ]);
                }
                // else{
                    $winner = SpinnerWinner::whereBetween('created_at',[date($filter_start),date($filter_end)])->first();
                // }
                $final['winner_info'] =[
                    'player_name' => $winner->full_name,
                    'player_id' =>  $winner->form_id
                ];
            }
            // dd($final);  
            //prasun dahal
            $year = date('Y');
            $month = 4;
            $day = 16;
            // if(strtotime(date('Y-m-d')) == strtotime(date($year . '-' . $month . '-'.$day))){
            //     $prasun_count = Form::where('full_name','Prasun Dahal')->count();
            //     if($prasun_count > 0){
            //         $prasun = Form::where('full_name','Prasun Dahal')->first()->toArray();
            //         $z =[
            //             'player_name' => $prasun['full_name'],
            //             'player_id' => $prasun['id']
            //         ];
            //         array_push($final['players_list'], $z);

            //         $final['winner_info'] =[
            //             'player_name' => $prasun['full_name'],
            //             'player_id' =>  $prasun['id']
            //         ];
            //     }
                
            // }
            //prasun dahal
        }
        // dd($final);
        $old_winners = SpinnerWinner::count();
        if($old_winners > 0){
            $old_winners = SpinnerWinner::limit(5)->get()->toArray();
            $final_old = [];
            $month = intval(date('m'));
            foreach($old_winners as $a => $b){
                $date = explode('-',date('Y-m-d',strtotime($b['created_at'])));
                if(intval($date[1]) != $month){
                    array_push($final_old,$b);
                }else{
                }
            }
            // $old_winners = $final_old;
            $old_list = $final_old;
        }else{
            $old_winners = [];
        }
        return view('newLayout.spinnerEncrypt', compact('final','form_token','old_list'));

        // return Response::json($final);
    }

    public function spinnerForm()
    {
        return 'Coming Soon :-)';
    }

     public function spinner()
    {
        $compare_amount = $this->limit_amount;
        try
        {
            $month = date('m');
            if($month >10){
                $month = '0'.$month;
            }
            $filter_start = '2022-'.$month.'-01';
            $filter_end = Carbon::now();

            $historys = History::where('type', 'load')
                                // ->where('created_at', '>', Carbon::now()
                                // ->subDays(30))                                
                                ->whereBetween('created_at',[date($filter_start),date($filter_end)])
                                ->select([DB::raw("SUM(amount_loaded) as total") , 'form_id as form_id'])
                                ->groupBy('form_id')
                                ->with('form')
                                ->whereHas('form')
                                ->get();
            $final = [
                'players_list' => [],
                'winner_info' => []
            ];
            if (($historys->count()) > 1)
            {
                $historys = $historys->toArray();
                foreach ($historys as $a => $b)
                {
                    if ($b['total'] >= $compare_amount)
                    {
                        $z =[
                            'player_name' => $b['form']['full_name'],
                            'player_id' => $b['form_id']
                        ];
                        array_push($final['players_list'], $z);
                    }
                }
                // if(){
                    
                // }
                if(!empty($final['players_list'])){
                    
                    $shuffle = array_rand($final['players_list']);
                    $final['winner_info'] =[
                        'player_name' => $final['players_list'][$shuffle]['player_name'],
                        'player_id' =>  $final['players_list'][$shuffle]['player_id']
                    ];
                }
                
                // dd($final['players_list']);

                //prasun dahal
                $year = date('Y');
                $month = 4;
                $day = 16;
                if(strtotime(date('Y-m-d')) == strtotime(date($year . '-' . $month . '-'.$day))){
                    $prasun_count = Form::where('full_name','Prasun Dahal')->count();
                    if($prasun_count > 0){
                        $prasun = Form::where('full_name','Prasun Dahal')->first()->toArray();
                        $z =[
                            'player_name' => $prasun['full_name'],
                            'player_id' => $prasun['id']
                        ];
                        array_push($final['players_list'], $z);

                        $final['winner_info'] =[
                            'player_name' => $prasun['full_name'],
                            'player_id' =>  $prasun['id']
                        ];
                    }
                    
                }
                //prasun dahal
            }

            // dd($shuffle,$final);
            return view('spinner', compact('final'));
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            dd($bug);
            return Response::json(['error' => $bug], 404);
        }
    }

    public function table()
    {
        try {
            $forms = Form::orderBy('full_name', 'asc')->get()->toArray();
            //  dd($forms);
            $games = Account::where('status', 'active')->get()->toArray();

            $activeGame = isset($_GET['game']) ? $_GET['game'] : '';
            $activeCashApp = isset($_GET['cash_app']) ? $_GET['cash_app'] : '';



            if (empty($activeGame)) {
                $activeGameDefault = Account::first()->toArray();
                $activeGame = $activeGameDefault['title'];
            }
            if (empty($activeCashApp)) {
                $cash_app_default = CashApp::first()->toArray();
                $activeCashApp = $cash_app_default['title'];
            }

            $activeGame = Account::where([['title', $activeGame], ['status', 'active']])
                ->with('formGames')
                ->first()
                ->toArray();
            // dd($activeGame);
            $cashApp = CashApp::where([['status', 'active']])
                ->get()
                ->toArray();

            $activeCashApp = CashApp::where([['title', $activeCashApp], ['status', 'active']])
                ->first()
                ->toArray();

            $final = [];
            if (!empty($activeGame['form_games'])) {
                foreach ($activeGame['form_games'] as $a => $b) {
                    $tip = FormTip::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $refer = FormRefer::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $cash = CashAppForm::where('form_id', $b['form']['id'])->where('cash_app_id', $activeCashApp['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $balance = FormBalance::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $redeem = FormRedeem::where('form_id', $b['form']['id'])->where('account_id', $activeGame['id'])->sum('amount');
                    $b['cash_app'] = $cash;
                    $b['tip'] = $tip;
                    $b['refer'] = $refer;
                    $b['balance'] = $balance;
                    $b['redeem'] = $redeem;
                    array_push($final, $b);
                }
                $activeGame['form_games'] = $final;
            }
            $history = History::where('account_id', $activeGame['id'])
                ->where('created_by', Auth::user()->id)
                ->with('form')
                ->with('account')
                ->with(['formGames' => function ($query) use ($activeGame) {
                    $query->where('account_id', $activeGame['id']);
                }])

                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
            // dd($activeCashApp);

            return view('newLayout.table', compact(
                'forms',
                'games',
                'activeGame',
                'history',
                'activeCashApp',
                'cashApp'
            ));
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            dd($bug);
            return Response::json(['error' => $bug], 404);
        }
    }

    public function removeFormGame(Request $request)
    {
        try
        {
            $gameId = $request->gameId;
            $userId = $request->userId;

            $account = Account::findOrFail($gameId);
            $user = Form::findOrFail($userId);
            $form_game = FormGame::where('account_id', $gameId)->where('form_id', $userId)->delete();
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }
        return Response::json('true');
    }
    public function tableUpdate(Request $request)
    {
        try
        {
            $gameId = $request->gameId;
            $userId = $request->userId;
            $amount = $request->amount;
            $cashAppId = $request->cashAppId;

            $account = Account::findOrFail($gameId);
            $user = Form::findOrFail($userId);
            $cashApp = CashApp::findOrFail($cashAppId);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }

        $accountBalance = $account->balance;
        $userBalance = $user->balance;
        // return $amount;
        $cashAppBalance = $cashApp->balance;
        $updateCashApp = CashApp::where('id', $cashAppId)->update(['balance' => ($cashAppBalance + $amount) ]);

        $cash_app_form = CashAppForm::create(['form_id' => $userId, 'cash_app_id' => $cashAppId, 'account_id' => $gameId, 'amount' => $amount, 'created_by' => Auth::user()->id]);

        $account = Account::where('id', $gameId)->update(['balance' => ($accountBalance - $amount) ]);
        // $user = Form::where('id', $userId)->update(['balance' => ($userBalance + $amount)]);
        $user_balance = FormBalance::create(['form_id' => $userId, 'account_id' => $gameId, 'amount' => $amount, 'created_by' => Auth::user()->id]);

        //update History
        $history = History::create(['form_id' => $userId, 'account_id' => $gameId, 'amount_loaded' => $amount, 'relation_id' => $user_balance->id, 'previous_balance' => $userBalance, 'final_balance' => $userBalance + $amount, 'type' => 'load', 'created_by' => Auth::user()->id]);
        // Log::channel('cronLog')->info('This is testing for ItSolutionStuff.com!'
        // $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        if ($user->balance != 1)
        {
            $currentMonth = date('m');
            $data = DB::table("histories")->where('form_id', $user->id)
                ->whereRaw('MONTH(created_at) = ?', [$currentMonth])->sum('amount_loaded');
            if ($data >= 600)
            {
                // dd($data >= 600);
                $token_id = Str::random(32);
                $form = Form::where('id', $user->id)
                    ->update(['balance' => 1, 'token' => $token_id]);
                $form = Form::where('id', $user->id)
                    ->first()
                    ->toArray();
                // try
                // {
                    // return Response::json($form);
                    // if (!empty($form['email']))
                    // {
                        // Mail::to($form['email'])->send(new crossedPlayers(json_encode($form)));
                        
                    // }
                    // if(!empty($form['phone'])){
                    // $boyname = $form['full_name'];

                    // $number  =  $form['full_name'];
                    // $basic = new \Vonage\Client\Credentials\Basic("e20bd554", "M5arJoXIrJ8Kat1r");
                    // $client = new \Vonage\Client($basic);

                    // $sendtextuser = 'Congratulation ' . $boyname . '!!! ' . ' You are now eligible for spinner';

                    // $message = $client->message()
                    //     ->send(['to' => '+9779813815279', 'from' => '18337222376', 'text' => $sendtextuser]);

                    // }
                    // Mail::to('riteshnoor69@gmail.com')->send(new crossedPlayers(json_encode($form)));
                    // Mail::to('prasundahal@gmail.com')->send(new crossedPlayers(json_encode($form)));
                    // Mail::to('joshibipin2052@gmail.com')->send(new crossedPlayers(json_encode($form)));
                // }
                // catch(\Exception $e)
                // {
                //     return Response::json($e->getMessage());
                // }
            }
        }
        return Response::json(Account::get()
            ->toArray());
    }
    public function referBalance(Request $request)
    {
        try
        {
            $gameId = $request->gameId;
            $userId = $request->userId;
            $amount = $request->amount;

            $account = Account::findOrFail($gameId);
            $user = Form::findOrFail($userId);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }

        $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        

        $account = Account::where('id', $gameId)->update(['balance' => ($accountBalance - $amount) ]);

        // $user = Form::where('id', $userId)->update(['balance' => ($userBalance + $amount)]);
        //create refer entry
        $refer = FormRefer::create(['form_id' => $userId, 'account_id' => $gameId, 'amount' => $amount, 'created_by' => Auth::user()->id]);

        //update History
        $history = History::create(['form_id' => $userId, 'account_id' => $gameId, 'relation_id' => $refer->id, 'amount_loaded' => $amount, 'previous_balance' => 0, 'final_balance' => 0, 'type' => 'refer', 'created_by' => Auth::user()->id]);
        // Log::channel('cronLog')->info('This is testing for ItSolutionStuff.com!'
        // $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        return Response::json(Account::get()
            ->toArray());
    }

    public function loadCashBalance(Request $request)
    {
        try
        {
            $cashAppId = $request->cashAppId;
            $userId = $request->userId;
            $amount = $request->amount;
            $gameId = $request->gameId;
            // CashApp
            // CashAppForm
            $account = Account::findOrFail($gameId);
            $cashApp = CashApp::findOrFail($cashAppId);
            $user = Form::findOrFail($userId);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }

        $cashAppBalance = $cashApp->balance;
        // $userBalance = $user->balance;
        

        $updateCashApp = CashApp::where('id', $cashAppId)->update(['balance' => ($cashAppBalance + $amount) ]);

        // $user = Form::where('id', $userId)->update(['balance' => ($userBalance + $amount)]);
        //create refer entry
        $cash_app_form = CashAppForm::create(['form_id' => $userId, 'cash_app_id' => $cashAppId, 'account_id' => $gameId, 'amount' => $amount, 'created_by' => Auth::user()->id]);

        //update History
        $history = History::create(['form_id' => $userId, 'account_id' => $gameId, 'amount_loaded' => $amount, 'relation_id' => $cash_app_form->id, 'cash_apps_id' => $cashAppId, 'previous_balance' => 0, 'final_balance' => 0, 'type' => 'cashAppLoad', 'created_by' => Auth::user()->id]);
        // Log::channel('cronLog')->info('This is testing for ItSolutionStuff.com!'
        // $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        return Response::json(Account::get()
            ->toArray());
    }
    public function redeemBalance(Request $request)
    {
        try
        {
            $gameId = $request->gameId;
            $userId = $request->userId;
            $amount = $request->amount;
            $cashAppId = $request->cashAppId;

            $account = Account::findOrFail($gameId);
            $user = Form::findOrFail($userId);
            $cashApp = CashApp::findOrFail($cashAppId);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }

        $accountBalance = $account->balance;
        $userBalance = $user->balance;
        $cashAppBalance = $cashApp->balance;

        $account = Account::where('id', $gameId)->update(['balance' => ($accountBalance + $amount) ]);
        // $user = Form::where('id', $userId)->update(['balance' => ($userBalance - $amount)]);
        $cashApp = CashApp::where('id', $cashAppId)->update(['balance' => ($cashAppBalance - $amount) ]);

        $form_redeem = FormRedeem::create(['form_id' => $userId, 'account_id' => $gameId, 'amount' => $amount, 'created_by' => Auth::user()->id]);

        //update History
        $history = History::create(['form_id' => $userId, 'account_id' => $gameId, 'cash_apps_id' => $cashAppId, 'amount_loaded' => $amount, 'relation_id' => $form_redeem->id, 'previous_balance' => $userBalance, 'final_balance' => $userBalance - $amount, 'type' => 'redeem', 'created_by' => Auth::user()->id]);

        // $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        return Response::json(Account::get()
            ->toArray());
    }
    public function tipBalance(Request $request)
    {
        try
        {
            $gameId = $request->gameId;
            $userId = $request->userId;
            $amount = $request->amount;

            $account = Account::findOrFail($gameId);
            $user = Form::findOrFail($userId);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }

        $accountBalance = $account->balance;

        $account = Account::where('id', $gameId)->update(['balance' => ($accountBalance + $amount) ]);
        // $user = Form::where('id', $userId)->update(['balance' => ($userBalance - $amount)]);
        // $cashApp = CashApp::where('id', $cashAppId)->update(['balance' => ($cashAppBalance - $amount)]);
        $form_redeem = FormTip::create(['form_id' => $userId, 'account_id' => $gameId, 'amount' => $amount, 'created_by' => Auth::user()->id]);

        //update History
        $history = History::create(['form_id' => $userId, 'account_id' => $gameId, 'relation_id' => $form_redeem->id, 'amount_loaded' => $amount, 'type' => 'tip', 'created_by' => Auth::user()->id]);

        // $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        return Response::json(Account::get()
            ->toArray());
    }
    public function addUser(Request $request)
    {
        try
        {
            $id = $request->id;
            $game_id = $request->game_id;
            $account_id = $request->account_id;

            $form = Form::findOrFail($id);
            $account = Account::findOrFail($account_id);

            $exists = FormGame::where([['form_id', $id], ['account_id', $account_id]])->count();
            if ($exists > 0)
            {
                return redirect()->back()
                    ->with('error', 'User Already exists in this game.');
            }
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }
        try
        {
            $form_game = FormGame::create(['form_id' => $id, 'account_id' => $account_id, 'game_id' => str_replace(' ', '_', strtolower($account['title'])) . '_' . $game_id, 'created_by' => Auth::user()->id, ]);
        }
        catch(\Illuminate\Database\QueryException $e)
        {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062)
            {
                return redirect()->back()
                    ->with('error', 'Game id already exists in this game.');
            }
        }
        //update History
        // $accountBalance = $account->balance;
        // $userBalance = $user->balance;
        return redirect()
            ->back()
            ->with('success', 'User Added');
    }
    public function report()
    {

        // with('account')
        // ->with('form')
        $history = History::with('form')->whereHas('form')
            ->with('account')
            ->whereHas('account')
        // ->whereBetween('created_at', [Carbon::now()->subMinutes(1440), now()])
        // ->with('created_by')
        
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $final = [];
        $forms = [];

        if (!empty($history))
        {
            $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
            foreach ($history as $a => $b)
            {
                if (!isset($final[$b['account_id']]))
                {
                    $final[$b['account_id']]['game_name'] = $b['account']['name'];
                    $final[$b['account_id']]['game_title'] = $b['account']['title'];
                    $final[$b['account_id']]['game_balance'] = $b['account']['balance'];
                    $final[$b['account_id']]['histories'] = [];
                    $final[$b['account_id']]['totals'] = $totals;
                    $final[$b['account_id']]['total_transactions'] = 1;
                }

                ($b['type'] == 'tip') ? ($final[$b['account_id']]['totals']['tip'] = $final[$b['account_id']]['totals']['tip'] + $b['amount_loaded']) : ($final[$b['account_id']]['totals']['tip'] = $final[$b['account_id']]['totals']['tip']);
                ($b['type'] == 'load') ? ($final[$b['account_id']]['totals']['load'] = $final[$b['account_id']]['totals']['load'] + $b['amount_loaded']) : ($final[$b['account_id']]['totals']['load'] = $final[$b['account_id']]['totals']['load']);
                ($b['type'] == 'redeem') ? ($final[$b['account_id']]['totals']['redeem'] = $final[$b['account_id']]['totals']['redeem'] + $b['amount_loaded']) : ($final[$b['account_id']]['totals']['redeem'] = $final[$b['account_id']]['totals']['redeem']);
                ($b['type'] == 'refer') ? ($final[$b['account_id']]['totals']['refer'] = $final[$b['account_id']]['totals']['refer'] + $b['amount_loaded']) : ($final[$b['account_id']]['totals']['refer'] = $final[$b['account_id']]['totals']['refer']);
                ($b['type'] == 'cashAppLoad') ? ($final[$b['account_id']]['totals']['cashAppLoad'] = $final[$b['account_id']]['totals']['cashAppLoad'] + $b['amount_loaded']) : ($final[$b['account_id']]['totals']['cashAppLoad'] = $final[$b['account_id']]['totals']['cashAppLoad']);
                $final[$b['account_id']]['total_transactions'] = $final[$b['account_id']]['total_transactions'] + 1;
                array_push($final[$b['account_id']]['histories'], $b);
            }
        }
        $details = $final;
        Mail::to('riteshnoor69@gmail.com')->send(new reportMail(json_encode($details)));
        Mail::to('prasundahal@gmail.com')->send(new reportMail(json_encode($details)));
        Mail::to('joshibipin2052@gmail.com')->send(new reportMail(json_encode($details)));
        // return view('mails.report',compact('details'));
        
    }
    function totals()
    {
        $history = History::with('account')->with('form')
            ->whereHas('form')
            ->with('created_by')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
        $final = [];
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $forms = [];

        if (!empty($history))
        {
            foreach ($history as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                if (!empty($form_game))
                {
                    $form_game->toArray();

                    $b['form_game'] = $form_game;

                    ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                    ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                    ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                    ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                    array_push($final, $b);
                    array_push($forms, $b['form']);
                }
            }
        }

        return $totals;
    }
    public function allHistory1()
    {

        
        if (Auth::user()->role == 'admin'){
            $history = History::where('created_by',Auth::user()->id)->orderBy('id', 'desc')->count();
        }else{
            $history = History::orderBy('id', 'desc')->count();
        }
        $final = [];
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $forms = [];

        $data = [['SN', 'Date', 'FB Name', 'Game', 'Game ID', 'Amount', 'Type', 'Creator']];
        if (($history > 0))
        {
            if (Auth::user()->role != 'admin'){
                $history = History::with('account')->with('form')->whereHas('form')->where('created_by',Auth::user()->id)->orderBy('id', 'desc')->with('created_by')->get()->toArray();
            }else{
                $history = History::with('account')->with('form')->whereHas('form')->with('created_by')->orderBy('id', 'desc')->get()->toArray();
            }
            foreach ($history as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                if (!empty($form_game))
                {
                    $form_game->toArray();

                    $b['form_game'] = $form_game;

                    ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                    ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                    ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                    ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                    array_push($final, $b);
                    // array_push($forms, $b['form']);
                }
            }
            $count = 1;
            foreach ($final as $key => $item)
            {
                $z = [$count++, date('d M,Y', strtotime($item['created_at'])) , $item['form']['facebook_name'], $item['form_game']['game_id'], $item['amount_loaded'], $item['type'], $item['created_by']['name']];
                array_push($data, $z);
            }

            // $activeGame['form_games'] = $final;
            
        }
        $games = Account::where('status', 'active')->get()
            ->toArray();
        // $filename = 'file.csv';
        // header('Content-Type: application/csv; charset=UTF-8');
        // header('Content-Disposition: attachment;filename="'.$filename.'";');
        // ob_clean();
        // flush();
        // $f = fopen('php://output', 'w');
        // foreach ($data as $line) {
        //     fputcsv($f, $line, ';');
        // }
        // $totals = [
        //     ['Total Tip','Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
        //     [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
        // ];
        $totals_2 = [['', ''], ['Total Tip', $totals['tip']], ['Total Balance', $totals['load']], ['Total Redeem', $totals['redeem']], ['Total Refer', $totals['refer']], ['Total Amount', $totals['cashAppLoad']], ['Total Profit', ($totals['load'] - $totals['redeem']) ],
        // 'Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
        // [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
        ];
        // foreach ($totals_2 as $line) {
        //     fputcsv($f, $line, ';');
        // }
        // fclose($f);
        // exit;
        $total = $totals;
        $forms = Form::get()->toArray();
        return view('newLayout.history', compact('final', 'total', 'games', 'forms'));
    }
    
    public function thisDayGame(Request $request)
    {
        $year = isset($request->year) ? $request->year : '';
        $month = isset($request->month) ? $request->month : '';
        $day = isset($request->day) ? $request->day : '';
        $game = isset($request->game) ? $request->game : '';
        $history = History::with('account')->with('form')
            ->whereHas('form')
            ->where('account_id',$game)
            ->with('created_by')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
        $grouped = [];
        if($month < 10){
            $month = '0'.$month;
        }
        if($day < 10){
            $day = '0'.$day;
        }
        $data = ['year' => $year, 'month' => $month, 'day' => $day];
        foreach ($history as $a => $b)
        {
            $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
            $created_at = explode('-', date('Y-m-d', strtotime($b['created_at'])));
            if (($created_at[0] == $year) && ($created_at[1] == $month) && ($created_at[2] == $day) && !empty($form_game))
            {
                $form_game->toArray();
                $b['form_game'] = $form_game;
                array_push($grouped, $b);
            }
        }
            // return Response::json($data);
        // return Response::json($data);
        return Response::json($grouped);
    }
    public function thisDay(Request $request)
    {
        $year = isset($request->year) ? $request->year : '';
        $month = isset($request->month) ? $request->month : '';
        $day = isset($request->day) ? $request->day : '';
        $category = isset($request->category) ? $request->category : '';
        $data = ['year' => $year, 'month' => $month, 'day' => $day];
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $account_totals = [];
        $history = History::with('form')
            ->with('created_by')
            ->with('account')
            ->whereHas('account', function ($query) use ($category) {
                if($category && $category != 'all')
                return $query->where('name', 'like', $category);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
        $grouped = [];
        $accounts = [];
        foreach ($history as $a => $b)
        {
            $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
            $created_at = explode('-', date('Y-m-d', strtotime($b['created_at'])));
            if (($created_at[0] == $year) && ($created_at[1] == $month) && ($created_at[2] == $day) && !empty($form_game))
            {
                $account_totals[$b['account_id']]['tip'] = isset($account_totals[$b['account_id']]['tip']) ? $account_totals[$b['account_id']]['tip'] : 0;
                $account_totals[$b['account_id']]['load'] = isset($account_totals[$b['account_id']]['load']) ? $account_totals[$b['account_id']]['load'] : 0;
                $account_totals[$b['account_id']]['redeem'] = isset($account_totals[$b['account_id']]['redeem']) ? $account_totals[$b['account_id']]['redeem'] : 0;
                $account_totals[$b['account_id']]['refer'] = isset($account_totals[$b['account_id']]['refer']) ? $account_totals[$b['account_id']]['refer'] : 0;
                $account_totals[$b['account_id']]['cashAppLoad'] = isset($account_totals[$b['account_id']]['cashAppLoad']) ? $account_totals[$b['account_id']]['cashAppLoad'] : 0;
                $form_game->toArray();

                $b['form_game'] = $form_game;
                array_push($grouped, $b);
                ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                ($b['type'] == 'tip') ? ($account_totals[$b['account_id']]['tip'] = $account_totals[$b['account_id']]['tip'] + $b['amount_loaded']) : ($account_totals[$b['account_id']]['tip'] = $account_totals[$b['account_id']]['tip']);
                ($b['type'] == 'load') ? ($account_totals[$b['account_id']]['load'] = $account_totals[$b['account_id']]['load'] + $b['amount_loaded']) : ($account_totals[$b['account_id']]['load'] = $account_totals[$b['account_id']]['load']);
                ($b['type'] == 'redeem') ? ($account_totals[$b['account_id']]['redeem'] = $account_totals[$b['account_id']]['redeem'] + $b['amount_loaded']) : ($account_totals[$b['account_id']]['redeem'] = $account_totals[$b['account_id']]['redeem']);
                ($b['type'] == 'refer') ? ($account_totals[$b['account_id']]['refer'] = $account_totals[$b['account_id']]['refer'] + $b['amount_loaded']) : ($account_totals[$b['account_id']]['refer'] = $account_totals[$b['account_id']]['refer']);
                ($b['type'] == 'cashAppLoad') ? ($account_totals[$b['account_id']]['cashAppLoad'] = $account_totals[$b['account_id']]['cashAppLoad'] + $b['amount_loaded']) : ($account_totals[$b['account_id']]['cashAppLoad'] = $totals['cashAppLoad']);

                if (!(isset($accounts[$b['account_id']])))
                {
                    $accounts[$b['account_id']] = 
                    [
                        'game_id' => $b['account']['id'], 
                        'game_name' => $b['account']['name'], 
                        'game_title' => $b['account']['title'], 
                        'game_balance' => $b['account']['balance'], 
                        'histories' => [],
                        'totals' => $totals, 
                        'total_transactions' => 0
                    ];
                }
                if(isset($accounts[$b['account_id']])) {
                    $accounts[$b['account_id']]['totals'] = $account_totals[$b['account_id']];
                }
            }
        }
        $default_accounts = Account::get()->toArray();
        $data['accounts'] = $accounts;
        $data['default_accounts'] = $default_accounts;
        $data['grouped'] = $grouped;
        // return Response::json($data);
        return Response::json($data);
    }
    public function filterUserHistoryAllData(Request $request)
    {
        $type = isset($request->filter_type) ? $request->filter_type : '';
        $year = isset($request->filter_year) ? $request->filter_year : '';
        $month = isset($request->filter_month) ? $request->filter_month : '';
        $day = isset($request->filter_day) ? $request->filter_day : '';
        $category = isset($request->filter_category) ? $request->filter_category : '';

        $history = History::with('account')->with('form')
            ->whereHas('account', function ($query) use ($category) {
                if($category && $category != 'all')
                return $query->where('name', 'like', $category);
            })
            ->with('created_by')
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
        $grouped = [];
        foreach ($history as $a => $b)
        {
            $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
            $created_at = explode('-', date('Y-m-d', strtotime($b['created_at'])));
            $b['created_at'] = date('Y-m-d', strtotime($b['created_at']));
            if(!empty($form_game)){
                $form_game->toArray();
                $b['form_game'] = $form_game;
            }
            
            if ($type == 'all')
            {
                if (($created_at[0] == $year) && ($created_at[1] == $month) && ($created_at[2] == $day))
                {
                    array_push($grouped, $b);
                }
            }
            else
            {
                if ($b['type'] == $type)
                {
                    if (($created_at[0] == $year) && ($created_at[1] == $month) && ($created_at[2] == $day))
                    {
                        array_push($grouped, $b);
                    }
                }
            }
        }
        return Response::json($grouped);
    }

     public function allData()
    {
        // Form::query()->update(['balance' => 0]);
        //get
        $year = isset($_GET['year']) ? $_GET['year'] : '';
        $month = isset($_GET['month']) ? $_GET['month'] : '';
        $sel_cat = isset($_GET['category']) && $_GET['category'] ? $_GET['category'] : '';
        $game_categories = Account::select('name')->distinct()->get();

        if (empty($year))
        {
            $year = date('Y');
        }
        if (empty($month))
        {
            $month = date('m');
        }
        if (Auth::user()->role != 'admin'){
            $history = History::whereHas('account', function ($query) {
                                    if(isset($_GET['category']) && $_GET['category'])
                                    return $query->where('name', 'like', $_GET['category']);
                                })
                                ->where('created_by',Auth::user()->id)
                                ->whereDate('created_at', '>=', date($year . '-' . $month . '-01'))
                                ->whereDate('created_at', '<=', date($year . '-' . $month . '-31'))
                                ->orderBy('id', 'desc')
                                ->count();
        }else{
            $history = History::whereHas('account', function ($query) {
                                    if(isset($_GET['category']) && $_GET['category'])
                                    return $query->where('name', 'like', $_GET['category']);
                                })
                                ->whereDate('created_at', '>=', date($year . '-' . $month . '-01'))
                                ->whereDate('created_at', '<=', date($year . '-' . $month . '-31'))
                                ->orderBy('id', 'desc')
                                ->count();
        }
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0, 'count' => 0];
        $grouped = [];
        if (($history > 0))
        {
                if (Auth::user()->role != 'admin'){
                    $history = History::whereHas('account', function ($query) {
                                    if(isset($_GET['category']) && $_GET['category'])
                                    return $query->where('name', 'like', $_GET['category']);
                                })
                                ->where('created_by',Auth::user()->id)
                                        ->whereDate('created_at', '>=', date($year . '-' . $month . '-01'))
                                        ->whereDate('created_at', '<=', date($year . '-' . $month . '-31'))
                                        ->orderBy('id', 'desc')
                                        ->get()
                                        ->toArray();
                }else{
                    $history = History::whereHas('account', function ($query) {
                                    if(isset($_GET['category']) && $_GET['category'])
                                    return $query->where('name', 'like', $_GET['category']);
                                })
                                ->whereDate('created_at', '>=', date($year . '-' . $month . '-01'))
                                        ->whereDate('created_at', '<=', date($year . '-' . $month . '-31'))
                                        ->orderBy('id', 'desc')
                                        ->get()
                                        ->toArray();
                }
            foreach ($history as $a => $b)
            {
                $created_at = explode('-', date('Y-m-d', strtotime($b['created_at'])));
                if (!(isset($grouped[$created_at[2]])))
                {
                    $grouped[$created_at[2]] = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0, 'count' => 0];
                }
                ($b['type'] == 'tip') ? ($grouped[$created_at[2]]['tip'] = $grouped[$created_at[2]]['tip'] + $b['amount_loaded']) : ($grouped[$created_at[2]]['tip'] = $grouped[$created_at[2]]['tip']);
                ($b['type'] == 'load') ? ($grouped[$created_at[2]]['load'] = $grouped[$created_at[2]]['load'] + $b['amount_loaded']) : ($grouped[$created_at[2]]['load'] = $grouped[$created_at[2]]['load']);
                ($b['type'] == 'redeem') ? ($grouped[$created_at[2]]['redeem'] = $grouped[$created_at[2]]['redeem'] + $b['amount_loaded']) : ($grouped[$created_at[2]]['redeem'] = $grouped[$created_at[2]]['redeem']);
                ($b['type'] == 'refer') ? ($grouped[$created_at[2]]['refer'] = $grouped[$created_at[2]]['refer'] + $b['amount_loaded']) : ($grouped[$created_at[2]]['refer'] = $grouped[$created_at[2]]['refer']);
                ($b['type'] == 'cashAppLoad') ? ($grouped[$created_at[2]]['cashAppLoad'] = $grouped[$created_at[2]]['cashAppLoad'] + $b['amount_loaded']) : ($grouped[$created_at[2]]['cashAppLoad'] = $grouped[$created_at[2]]['cashAppLoad']);
                $grouped[$created_at[2]]['count'] += 1;

                ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

            }

            // return view('newLayout.alldata', compact('grouped', 'month', 'year','total'));
            
        }
        $total = $totals;
        $history = [];

        $all_months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
        // dd($grouped,$year,$month,$history,$totals);
        $forms = Form::orderBy('id', 'desc')->get()->toArray();
        $games = Account::orderBy('id', 'desc')->get()->toArray();

        
        $form_games = FormGame::orderBy('id', 'desc')->with('form')->whereHas('form')->get()->toArray();
        // dd($form_games);
        return view('newLayout.alldata', compact('grouped', 'month', 'year', 'form_games','total', 'all_months','forms','games','game_categories','sel_cat'));
    }
    public function allData1()
    {

        $history = History::with('account')->with('created_by')
            ->orderBy('created_at', 'asc')
            ->count();
        $final = [];
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $forms = [];

        $data = [['SN', 'Date', 'FB Name', 'Game', 'Game ID', 'Amount', 'Type', 'Creator']];
        if (($history > 0))
        {
            $history = History::with('account')->with('form')
                ->whereHas('form')
                ->with('created_by')
            // ->whereDate('created_at', Carbon::today())
            
                ->orderBy('created_at', 'asc')
                ->get()
            // ->groupby('id')
            
                ->toArray();
            // $visitors = History::select('*')
            //     ->orderBy('created_at')
            //     ->groupBy(DB::raw("DATE_FORMAT(created_at, '%m-%Y')"))
            //     ->get();
            // dd($history);
            // $history = DB::table('histories')
            // ->groupBy(DB::raw("DATE_FORMAT(created_at, '%m-%Y')"))
            // ->get();
            $grouped = [];
            foreach ($history as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                $created_at = explode('-', date('Y-m-d', strtotime($b['created_at'])));

                if (!(isset($grouped[$created_at[0]])))
                {
                    $grouped[$created_at[0]] = [];
                }

                if (!(isset($grouped[$created_at[0]][$created_at[1]])))
                {
                    $grouped[$created_at[0]][$created_at[1]] = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
                }

                // if(isset($grouped[$created_at[0]])){
                //     $grouped[$created_at[0]][$created_at[1]] = [];
                // }
                if (!empty($form_game))
                {
                    $form_game->toArray();

                    $b['form_game'] = $form_game;

                    ($b['type'] == 'tip') ? ($grouped[$created_at[0]][$created_at[1]]['tip'] = $grouped[$created_at[0]][$created_at[1]]['tip'] + $b['amount_loaded']) : ($grouped[$created_at[0]][$created_at[1]]['tip'] = $grouped[$created_at[0]][$created_at[1]]['tip']);
                    ($b['type'] == 'load') ? ($grouped[$created_at[0]][$created_at[1]]['load'] = $grouped[$created_at[0]][$created_at[1]]['load'] + $b['amount_loaded']) : ($grouped[$created_at[0]][$created_at[1]]['load'] = $grouped[$created_at[0]][$created_at[1]]['load']);
                    ($b['type'] == 'redeem') ? ($grouped[$created_at[0]][$created_at[1]]['redeem'] = $grouped[$created_at[0]][$created_at[1]]['redeem'] + $b['amount_loaded']) : ($grouped[$created_at[0]][$created_at[1]]['redeem'] = $grouped[$created_at[0]][$created_at[1]]['redeem']);
                    ($b['type'] == 'refer') ? ($grouped[$created_at[0]][$created_at[1]]['refer'] = $grouped[$created_at[0]][$created_at[1]]['refer'] + $b['amount_loaded']) : ($grouped[$created_at[0]][$created_at[1]]['refer'] = $grouped[$created_at[0]][$created_at[1]]['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($grouped[$created_at[0]][$created_at[1]]['cashAppLoad'] = $grouped[$created_at[0]][$created_at[1]]['cashAppLoad'] + $b['amount_loaded']) : ($grouped[$created_at[0]][$created_at[1]]['cashAppLoad'] = $grouped[$created_at[0]][$created_at[1]]['cashAppLoad']);

                    // array_push($final, $b);
                    // array_push($forms, $b['form']);
                    
                }
            }

            $count = 1;
            foreach ($final as $key => $item)
            {
                $z = [$count++, date('d M,Y', strtotime($item['created_at'])) , $item['form']['facebook_name'], $item['form_game']['game_id'], $item['amount_loaded'], $item['type'], $item['created_by']['name']];
                array_push($data, $z);
            }

            // $activeGame['form_games'] = $final;
            
        }
        $games = Account::where('status', 'active')->get()
            ->toArray();
        // $filename = 'file.csv';
        // header('Content-Type: application/csv; charset=UTF-8');
        // header('Content-Disposition: attachment;filename="'.$filename.'";');
        // ob_clean();
        // flush();
        // $f = fopen('php://output', 'w');
        // foreach ($data as $line) {
        //     fputcsv($f, $line, ';');
        // }
        // $totals = [
        //     ['Total Tip','Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
        //     [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
        // ];
        $totals_2 = [['', ''], ['Total Tip', $totals['tip']], ['Total Balance', $totals['load']], ['Total Redeem', $totals['redeem']], ['Total Refer', $totals['refer']], ['Total Amount', $totals['cashAppLoad']], ['Total Profit', ($totals['load'] - $totals['redeem']) ],
        // 'Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
        // [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
        ];
        // foreach ($totals_2 as $line) {
        //     fputcsv($f, $line, ';');
        // }
        // fclose($f);
        // exit;
        $total = $totals;
        dd($final);
        return view('newLayout.history', compact('final', 'total', 'games', 'forms'));
    }
    public function todaysHistory()
    {
        if (Auth::user()->role != 'admin'){
            $history = History::where('created_by',Auth::user()->id)->orderBy('id', 'desc')->count();
        }else{
            $history = History::orderBy('id', 'desc')->count();
        }
        $final = [];
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $forms = [];

        $data = [['SN', 'Date', 'FB Name', 'Game', 'Game ID', 'Amount', 'Type', 'Creator']];
        if (($history > 0))
        {
            
            if (Auth::user()->role != 'admin'){
                $history = History::with('account')->with('form')->whereHas('form')->where('created_by',Auth::user()->id)->orderBy('id', 'desc')->whereDate('created_at', Carbon::today())->with('created_by')->get()->toArray();
            }else{
                $history = History::with('account')->with('form')->whereHas('form')->with('created_by')->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get()->toArray();
            }
            // $history = History::with('account')->with('form')
            //     ->whereHas('form')
            //     ->with('created_by')
            //     ->whereDate('created_at', Carbon::today())
            //     ->orderBy('id', 'desc')
            //     ->get()
            //     ->toArray();
            foreach ($history as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                if (!empty($form_game))
                {
                    $form_game->toArray();

                    $b['form_game'] = $form_game;

                    ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                    ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                    ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                    ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                    array_push($final, $b);
                    array_push($forms, $b['form']);
                }
            }
            $count = 1;
            foreach ($final as $key => $item)
            {
                $z = [$count++, date('d M,Y', strtotime($item['created_at'])) , $item['form']['facebook_name'], $item['form_game']['game_id'], $item['amount_loaded'], $item['type'], $item['created_by']['name']];
                array_push($data, $z);
            }

            // $activeGame['form_games'] = $final;
            
        }
        $games = Account::where('status', 'active')->get()
            ->toArray();
        // $filename = 'file.csv';
        // header('Content-Type: application/csv; charset=UTF-8');
        // header('Content-Disposition: attachment;filename="'.$filename.'";');
        // ob_clean();
        // flush();
        // $f = fopen('php://output', 'w');
        // foreach ($data as $line) {
        //     fputcsv($f, $line, ';');
        // }
        // $totals = [
        //     ['Total Tip','Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
        //     [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
        // ];
        $totals_2 = [['', ''], ['Total Tip', $totals['tip']], ['Total Balance', $totals['load']], ['Total Redeem', $totals['redeem']], ['Total Refer', $totals['refer']], ['Total Amount', $totals['cashAppLoad']], ['Total Profit', ($totals['load'] - $totals['redeem']) ],
        // 'Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
        // [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
        ];
        // foreach ($totals_2 as $line) {
        //     fputcsv($f, $line, ';');
        // }
        // fclose($f);
        // exit;
        $total = $totals;
        return view('newLayout.history', compact('final', 'total', 'games', 'forms'));
    }
    public function allHistory()
    {

        $history = History::with('account')->with('form')
            ->whereHas('form')
            ->with('created_by')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
        $final = [];
        if (!empty($history))
        {
            foreach ($history as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first()
                    ->toArray();
                $b['form_game'] = $form_game;

                array_push($final, $b);
            }
            // $activeGame['form_games'] = $final;
            
        }
        return Response::json($final);
    }
    public function undoHistory()
    {

        $history = History::with('account')->with('form')
            ->whereHas('form')
            ->with('created_by')
            ->orderBy('id', 'desc')
            ->take(100)
            ->get()
            ->toArray();
        $final = [];
        if (!empty($history))
        {
            foreach ($history as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                if ($form_game)
                {
                    $form_game->toArray();
                    $b['form_game'] = $form_game;
                    array_push($final, $b);
                }
            }
            // $activeGame['form_games'] = $final;
            
        }
        return Response::json($final);
    }
    public function undoTable($id)
    {
        try
        {
            $history = History::findOrFail($id);

            $related_id = $history->relation_id;
            $type = $history->type;
            $account_id = $history->account_id;
            $cash_apps_id = $history->cash_apps_id;
            $amount = $history->amount_loaded;
            // $related = FormRedeem::where('id',$related_id)->get();
            //  dd($related);
            if ($type == 'tip')
            {
                $related = FormTip::find($related_id)->delete();
            }
            elseif ($type == 'redeem')
            {
                $related = FormRedeem::find($related_id)->delete();
                $account = Account::findOrFail($account_id);
                $cashApp = CashApp::findOrFail($cash_apps_id);
                $account = Account::where('id', $account_id)->update(['balance' => ($account->balance - $amount) ]);
                $cashApp = CashApp::where('id', $cash_apps_id)->update(['balance' => ($cashApp->balance + $amount) ]);
            }
            elseif ($type == 'refer')
            {
                $related = FormRefer::find($related_id)->delete();
                $account = Account::findOrFail($account_id);
                $accountBalance = $account->balance;
                $account = Account::where('id', $account_id)->update(['balance' => ($accountBalance + $amount) ]);
            }
            elseif ($type == 'load')
            {
                $related = FormBalance::find($related_id)->delete();
                $account = Account::findOrFail($account_id);
                $accountBalance = $account->balance;
                $account = Account::where('id', $account_id)->update(['balance' => ($accountBalance + $amount) ]);
            }
            elseif ($type == 'cashAppLoad')
            {
                $related = CashAppForm::find($related_id)->delete();
                $cashApp = CashApp::findOrFail($cash_apps_id);
                $cashAppBalance = $cashApp->balance;
                $updateCashApp = CashApp::where('id', $cash_apps_id)->update(['balance' => ($cashAppBalance - $amount) ]);
            }
            // switch($type) {
            //     case('tip'):
            //     case('redeem'):
            //     case('refer'):
            //         case('load'):
            //     case('cashAppLoad'):
            // }
            $history->delete();
            return redirect(route('table'))
                ->with('success', "Transaction undo successful");
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }

        return redirect(route('table'))->with('success', "Transaction undo successful");
    }
    public function userHistory(Request $request)
    {
        $type = $request->type;
        $getType = $request->getType;

        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $final = [];
        if (!empty($getType) && $getType == 'all')
        {
            $history = History::where('form_id', $request->userId)
                ->with('account')
                ->with('created_by')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();

            if (!empty($history))
            {
                foreach ($history as $a => $b)
                {
                    $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first()
                        ->toArray();
                    $b['form_game'] = $form_game;
                    ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                    ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                    ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                    ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                    array_push($final, $b);
                }
                $totals['profit'] = $totals['load'] - $totals['redeem'];
            }
            return Response::json([$final, $totals]);
        }
        if (!empty($type))
        {
            $history = History::where('account_id', $request->game)
                ->where('form_id', $request->userId)
                ->where('type', $request->type)
                ->with('created_by')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
        }
        else
        {

            $history = History::where('account_id', $request->game)
                ->where('form_id', $request->userId)
                ->with('created_by')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
        }
        // return Response::json([$request->game,$request->userId,$request->type]);
        return Response::json($history);
    }

    public function filterUserHistory(Request $request)
    {
        $filter_type = $request->filter_type;
        $userId = $request->userId;
        $game = $request->game;
        $filter_start = $request->filter_start;
        $filter_end = $request->filter_end;
        $historyType = $request->historyType;

        $history = History::query();

        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];

        if ($filter_type != 'all')
        {
            $history->where('type', $request->filter_type);
        }

        // $history->when(request('filter_type', '!=','all'), function ($q, $filter_type) {
        //     return $q->where('type',$filter_type);
        // });
        // return Response::json($historys);
        if ($filter_start != '')
        {
            $history->whereDate('created_at', '>=', $filter_start);
        }
        if ($filter_end != '')
        {
            $history->whereDate('created_at', '<=', $filter_end);
        }
        if ($historyType == '')
        {
            $history->where('account_id', $game);
        }

        $history->where('form_id', $userId)->with('account')
            ->with('created_by')
            ->orderBy('id', 'desc');
        // if($filter_end != ''){
        //     $history->where('type',$request->filter_type);
        // }
        // $history->whereBetween('created_at',[date($filter_start),date($filter_end)]);
        // $history->whereDate('created_at','<=', $filter_start)->whereDate('created_at','>=', $filter_end);
        // if($filter_start != ''){
        //     $history->where('type',$request->filter_type);
        // }
        // if($filter_end != ''){
        //     $history->where('type',$request->filter_type);
        // }
        $final = [];
        $historys = $history->get()
            ->toArray();
        //         ->orderBy('id','desc')
        //         ->get()
        //         ->toArray();
        if (!empty($historys))
        {
            foreach ($historys as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first()
                    ->toArray();
                $b['form_game'] = $form_game;
                ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                array_push($final, $b);
            }
            $totals['profit'] = $totals['load'] - $totals['redeem'];
        }

        // return Response::json([$final,$totals]);
        // $data = [
        //     'filter_type' => $filter_type,
        //     'userId' => $userId,
        //     'game' => $game,
        //     'filter_start' => $filter_start,
        //     'filter_end' => $filter_end,
        //     'history' => $historys,
        // ];
        return Response::json([$final, $totals]);
        return Response::json($data);
    }

    public function export(Request $request)
    {
        $filter_type = $request->filter_type;
        $filter_start = $request->filter_start;
        $filter_end = $request->filter_end;
        $filter_game = $request->filter_game;
        $filter_user = $request->filter_user;

        $history = History::query();

        if ($filter_game != 'all')
        {
            $history->where('account_id', '=', $filter_game);
        }
        if ($filter_user != 'all')
        {
            $history->where('form_id', '=', $filter_user);
        }
        if ($filter_start != '')
        {
            $history->whereDate('created_at', '>=', $filter_start);
        }
        if ($filter_end != '')
        {
            $history->whereDate('created_at', '<=', $filter_end);
        }

        $history->with('form')
            ->whereHas('form')
            ->with('account')
            ->with('created_by')
            ->orderBy('id', 'desc');

        $historys = $history->get()
            ->toArray();

        $final = [];
        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
        $forms = [];

        $data = [
        // ['SN', 'Date', 'FB Name','Game','Game ID','Amount','Type','Creator']
        ];
        if (!empty($historys))
        {
            foreach ($historys as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first();
                if (!empty($form_game))
                {
                    $form_game->toArray();
                    $b['form_game'] = $form_game;

                    ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                    ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                    ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                    ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);

                    array_push($final, $b);
                    array_push($forms, $b['form']);
                }
            }
            $count = 1;
            foreach ($final as $key => $item)
            {
                $z = [$count++, date('d M,Y', strtotime($item['created_at'])) , $item['form']['facebook_name'], $item['account']['name'], $item['form_game']['game_id'], $item['amount_loaded'], $item['type'], $item['created_by']['name']];
                array_push($data, $z);
            }
            // return $data;
            // $filename = 'file.csv';
            // header('Content-Type: application/csv; charset=UTF-8');
            // header('Content-Disposition: attachment;filename="'.$filename.'";');
            // ob_clean();
            // flush();
            // $f = fopen('php://output', 'w');
            // foreach ($data as $line) {
            // fputcsv($f, $line, ';');
            // }
            $totals_2 = [['', ''], ['Total Tip', $totals['tip']], ['Total Balance', $totals['load']], ['Total Redeem', $totals['redeem']], ['Total Refer', $totals['refer']], ['Total Amount', $totals['cashAppLoad']], ['Total Profit', ($totals['load'] - $totals['redeem']) ],
            // 'Total Balance','Total Redeem','Total Refer','Total Amount','Total Profit'],
            // [$totals['tip'],$totals['load'],$totals['redeem'],$totals['refer'],$totals['cashAppLoad'],($totals['load'] - $totals['redeem'])],
            ];
            // foreach ($totals_2 as $line) {
            //     fputcsv($f, $line, ';');
            // }
            // fclose($f);
            // exit;
            return [$data, $totals_2];
            // return Response::json('success');
            // $activeGame['form_games'] = $final;
            
        }
        else
        {
            return Response::json(['error' => 'History is Empty'], 404);
        }
        return Response::json(['error' => 'Something went wrong'], 404);
    }
    public function filterAllHistory(Request $request)
    {
        $filter_type = $request->filter_type;
        $filter_start = $request->filter_start;
        $filter_end = $request->filter_end;
        $filter_game = $request->filter_game;
        $filter_user = $request->filter_user;

        $history = History::query();

        if ($filter_type != 'all')
        {
            $history->where('type', $request->filter_type);
        }

        // $history->when(request('filter_type', '!=','all'), function ($q, $filter_type) {
        //     return $q->where('type',$filter_type);
        // });
        // return Response::json($historys);
        if ($filter_game != 'all')
        {
            $history->where('account_id', '=', $filter_game);
        }
        if ($filter_user != 'all')
        {
            $history->where('form_id', '=', $filter_user);
        }
        if ($filter_start != '')
        {
            $history->whereDate('created_at', '>=', $filter_start);
        }
        if ($filter_end != '')
        {
            $history->whereDate('created_at', '<=', $filter_end);
        }

        $history->with('form')
            ->whereHas('form')
            ->with('account')
            ->with('created_by')
            ->orderBy('id', 'desc');

        $historys = $history->get();

        $final = [];

        $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0, 'profit' => 0];
        if (!empty($historys))
        {
            foreach ($historys as $a => $b)
            {
                $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->count();
                if ($form_game > 0)
                {
                    $form_game = FormGame::where('form_id', $b['form_id'])->where('account_id', $b['account_id'])->first()
                        ->toArray();
                    $b['form_game'] = $form_game;

                    ($b['type'] == 'tip') ? ($totals['tip'] = $totals['tip'] + $b['amount_loaded']) : ($totals['tip'] = $totals['tip']);
                    ($b['type'] == 'load') ? ($totals['load'] = $totals['load'] + $b['amount_loaded']) : ($totals['load'] = $totals['load']);
                    ($b['type'] == 'redeem') ? ($totals['redeem'] = $totals['redeem'] + $b['amount_loaded']) : ($totals['redeem'] = $totals['redeem']);
                    ($b['type'] == 'refer') ? ($totals['refer'] = $totals['refer'] + $b['amount_loaded']) : ($totals['refer'] = $totals['refer']);
                    ($b['type'] == 'cashAppLoad') ? ($totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded']) : ($totals['cashAppLoad'] = $totals['cashAppLoad']);
                    // switch($b['type']) {
                    // case('tip'):
                    //     $totals['tip'] = $totals['tip'] + $b['amount_loaded'];
                    //     case('load'):
                    //         $totals['load'] = $totals['load'] + $b['amount_loaded'];
                    //     case('redeem'):
                    //         $totals['redeem'] = $totals['redeem'] + $b['amount_loaded'];
                    //     case('refer'):
                    //         $totals['refer'] = $totals['refer'] + $b['amount_loaded'];
                    //     case('cashAppLoad'):
                    //         $totals['cashAppLoad'] = $totals['cashAppLoad'] + $b['amount_loaded'];
                    // }
                    array_push($final, $b);
                }

            }
            $totals['profit'] = $totals['load'] - $totals['redeem'];
            // $activeGame['form_games'] = $final;
            
        }

        // $data = [
        //     'filter_type' => $filter_type,
        //     'userId' => $userId,
        //     'game' => $game,
        //     'filter_start' => $filter_start,
        //     'filter_end' => $filter_end,
        //     'history' => $historys,
        // ];
        // dd();
        // $final['totals'] = $totals;
        return Response::json([$final, $totals]);
    }
    public function games()
    {
        $total = Account::count();
        $forms = Account::orderBy('id', 'desc')->get();
        $trashed = Account::onlyTrashed()->orderBy('id', 'desc')
            ->get()
            ->toArray();
        return view('newLayout.games', compact('total', 'forms', 'trashed'));
    }
       public function gameImage($id){
        if($id != ''){
            $game = Account::where('id',$id)->first();
            return view('newLayout.gameImages',compact('game'));
        }
        return back();
    }
      public function gameExtraImageStore(Request $request){
        // dd($request->file('file'));
        if ($request->hasFile('file'))
        {
            $game = Account::where('id',$request->id)->first();
            if($game->extra_images == ''){
                $array = [];
            }else{
                $array = explode(',',$game->extra_images);
            }
            
            foreach ($request->file('file') as $file) {
                $image_name = md5(rand(10,100));
                $ext = strtolower($file->getClientOriginalExtension());
                $image_full_name = $image_name.'.'.$ext;
                $file->move(base_path() . '/public/uploads/games/',$image_full_name);

                // $img = $request->file('file');
                // $name = time() . '.' . $img->extension().rand(10,100);
                // $img->move(base_path() . '/public/uploads/', $name);
                array_push($array,$image_full_name);
            }
            $game->extra_images = implode(',',$array);
            try{
                $game->save();
                return back()->with('success', 'Images Saved');

            }catch(\Exception $e){
                $bug = $e->getMessage();
                return back()->with('error', $bug);
            }
        }
    }
    public function gameImageDestroy($id,$key){
        try
        {
            $form = Account::where('id', $id)->first();
            $extra_images = $form->extra_images;
            if(!empty($extra_images)){
                $extra_images = explode(',',$extra_images);
                if(isset($extra_images[$key])){
                    unset($extra_images[$key]);
                    $form->extra_images = implode(',',$extra_images);
            // dd($extra_images);
                    $form->save();
                    return back()->with('message', " Deleted Successfully");
                }else{
                    return back()->with('error', 'Image Not Found');
                }
            }else{                
                return back()->with('error', 'Images Empty');
            }
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return back()->with('error', $bug);
        }
    }
    public function gameDestroy($id)
    {
        try
        {
            $form = Account::findOrFail($id);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect(route('gamers'))
                ->with('error', $bug);
        }
        if ($form->delete() == true)
        {
            $delete_form_balance = FormBalance::where('form_id', $id)->delete();
            return Response::json('true');
        }
        else
        {
            return Response::json(['error' => $bug], 404);
        }
    }
    public function gameEdit($id)
    {
        $form = Account::where('id', $id)->first();
        $html = view('new.gameEditModal', compact('form'))->render();
        return Response::json($html);
    }

    public function gamerUpdateBalance(Request $request)
    {
        // $request = $_POST[];
        // return Response::json($id);
        $id = $request->game_id;
        try
        {
            $form = Account::findOrFail($id);
            $form->balance = isset($request['game_balance']) ? ($form->balance + $request['game_balance']) : null;
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()
                ->back()
                ->with('error', $bug);
        }
        if ($form->save() == true)
        {
            return redirect()
                ->back()
                ->with('success', 'Game Updated');
        }
        else
        {
            return redirect()
                ->back()
                ->with('error', $form);
        }
    }
    public function gameUpdate(Request $request, $id)
    {
        $request = $_POST['data'];
        // return Response::json($id);
        try
        {
            $form = Account::findOrFail($id);

            if (!(isset($request['name']) && !empty($request['name'])))
            {
                return Response::json(['error' => 'Name is Empty'], 404);
            }
            if (!(isset($request['title']) && !empty($request['title'])))
            {
                return Response::json(['error' => 'Title is Empty'], 404);
            }
            if (!(isset($request['balance']) && !empty($request['balance'])))
            {
                return Response::json(['error' => 'Balance is Empty'], 404);
            }
            $form->name = isset($request['name']) ? $request['name'] : null;
            $form->title = isset($request['title']) ? $request['title'] : null;
            $form->balance = isset($request['balance']) ? $request['balance'] : null;
            $form->status = isset($request['status']) ? $request['status'] : 'inactive';
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }
        if ($form->save() == true)
        {
            return Response::json($form);
        }
        else
        {
            return Response::json(['error' => $form], 404);
        }
    }

    public function gameStore(Request $request)
    {
        // $request = $_POST;
        try
        {
            $game = new Account();
            $game->name = $request->name;
            $game->title = $request->title;
            $game->balance = $request->balance;
            $game->status = $request->status;
            if ($request->hasFile('file'))
            {
                $img = $request->file('file');
                $name = time() . '.' . $img->extension();
                $img->move(base_path() . '/public/uploads/', $name);
                $game->image = $name;
            }
            // $game = Account::create([
            //     'name' => $request->name,
            //     'title' => $request->title,
            //     'balance' => $request->balance,
            //     'status' => $request->status,
            // ]);
            $game->save();
            return redirect()
                ->back()
                ->with('success', 'Game Created');
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()
                ->back()
                ->with('error', $bug);
            // return Response::json(['error' => $bug],404);
            
        }
    }
    public function gameImageStore(Request $request)
    {
        // $request = $_POST;
        try
        {
            $game = Account::find($request->id);
            if ($request->hasFile('file'))
            {
                $img = $request->file('file');
                $name = time() . '.' . $img->extension();
                $img->move(base_path() . '/public/uploads/', $name);
                $game->image = $name;
            }
            $game->save();
            return redirect()
                ->back()
                ->with('success', 'Image Updated');
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()
                ->back()
                ->with('error', $bug);
            // return Response::json(['error' => $bug],404);
            
        }
    }
      public function updateForm(Request $request){
        try
        {
            $form = Form::findOrFail($request->cid);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        }
        if($request->type == 'number'){ 
            Form::where('id',$request->cid)->update([
                'number' => $request->note
            ]);
        }else{
            Form::where('id',$request->cid)->update([
                'email' => $request->note
            ]);

        }
        return Response::json('true');
    }
    public function getHistory($id){
        $month = date('m');
        if($month >10){
            $month = '0'.$month;
        }
        $filter_start = '2022-'.$month.'-01';
        $filter_end = Carbon::now();
        $history = History::with('account')
                            ->where('form_id',$id)
                            ->with('form')
                            ->whereHas('form')
                            ->whereBetween('created_at',[date($filter_start),date($filter_end)])
                            ->with('created_by')
                            ->orderBy('id', 'desc')
                            ->get()
                            ->toArray();

        $final = [];
        $forms = [];
        if (!empty($history))
        {
            foreach ($history as $a => $b)
            {
                $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
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
                        $final[$b['form_id']]['full_name'] = $form['full_name'];
                        $final[$b['form_id']]['number'] = $form['number'];
                        $final[$b['form_id']]['email'] = $form['email'];
                        $final[$b['form_id']]['facebook_name'] = $form['facebook_name'];
                    }

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
        return $final;
    }
      public function sendMessage(Request $request){
               
        $type = $request->type;
        $message = $request->message;
        $limit_amount = $this->limit_amount;

        if($request->id > 0){
            try
                {
                    $input2 = self::getHistory($request->id);
                    // $input = Form::where('id',$request->id)->first()->toArray();
                    
                    foreach($input2 as $a => $input){
                        
                    $token_id = Str::random(32);
                    $form = Form::where('id', $input['form_id'])
                        ->update(['balance' => 1, 'token' => $token_id]);
                    $form = [
                        'name' => $input['full_name'],
                        'message' => $message,
                        'message-end' => '',
                        'limit_amount' => $limit_amount,
                        'load' => $input['totals']['load'],
                        'type' => $type,
                        'subject' => '',
                        'token_id' => $token_id,
                        'load-remaining' => 0
                    ];
                    if($type == 'above-'.$limit_amount){
                        if($form['load']  >= $limit_amount){
                             $form['subject'] = 'Noor Games - Eligible For Spinner';
                             try
                                 {
                                     Mail::to($input['email'])->send(new spinnerBulkMail(json_encode($form)));
                                     Log::channel('spinnerBulk')->info("Mail sent successfully to ".$input['email']);
                                     return redirect()->back()->withInput()->with('success', 'Mail Sent');
                                 }
                             catch(\Exception $e)
                                 {
                                     $bug = $e->getMessage();
                                     Log::channel('spinnerBulk')->info($bug);
                                     return redirect()->back()->withInput()->with('error', $bug);
                                 }
                         }
                     }elseif($type == 'below-'.$limit_amount){
                         if($form['load']  <  $limit_amount){
                             $form['subject'] = 'Noor Games - Spinner';
                             // $form['message-end'] = 'Noor Games - Eligible For Spinner';              
                             $form['load-remaining'] = $limit_amount - $form['load'];
         
                             try
                             {
                                 Mail::to($input['email'])->send(new spinnerBulkMail(json_encode($form)));
                                 Log::channel('spinnerBulk')->info("Mail sent successfully to ".$input['email']);
                                 return redirect()->back()->withInput()->with('success', 'Mail Sent');
                             }
                             catch(\Exception $e)
                             {
                                 $bug = $e->getMessage();
                                 Log::channel('spinnerBulk')->info($bug);
                                 return redirect()->back()->withInput()->with('error', $bug);
                             }
                         }
                     }else{
                         $limit_1 = $limit_amount - 50;
                         $limit_2 = $limit_amount;
                         if($form['load']  >= $limit_1){
                             if($form['load']  < $limit_2){  
                                 $form['subject'] = 'Noor Games - Almost Eligible For Spinner';                    
                                 $form['load-remaining'] = $limit_amount - $form['load'];
                                 
                                 try
                                 {
                                     Mail::to($input['email'])->send(new spinnerBulkMail(json_encode($form)));
                                     Log::channel('spinnerBulk')->info("Mail sent successfully to ".$input['email']);
                                     return redirect()->back()->withInput()->with('success', 'Mail Sent');
                                 }
                                 catch(\Exception $e)
                                 {
                                     $bug = $e->getMessage();
                                     Log::channel('spinnerBulk')->info($bug);
                                     return redirect()->back()->withInput()->with('error', $bug);
                                 }
                             }
                         }
                     }

                    }
                    
                    // Mail::to($form['email'])->send(new spinnerBulkMail(json_encode($form)));
                    // Log::channel('spinnerBulk')->info("Mail sent successfully to ".$input['email']);
                }
            catch(\Exception $e)
                {
                    $bug = $e->getMessage();
                    Log::channel('spinnerBulk')->info($bug);
                    return redirect()->back()->withInput()->with('error', $bug);
                }
        }
        
        $limit_amount = $this->limit_amount;
        $details = [
            'subject' => 'Weekly Notification',
            'type' => $type,
            'message'=> $message,
            'limit_amount' => $limit_amount
        ];
       
        try
        {
            $job = (new \App\Jobs\SpinnerBulkMessage($details))
                ->delay(
                    now()
                    ->addSeconds(2)
                ); 
            dispatch($job);
            return redirect()->back()->withInput()->with('success', 'Mail Sent');
            // \Artisan::call('queue:listen');
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error',$bug);
        }
    }
     public function sendSMS(Request $request){
        // Yayyy '; 
        $type = $request->type;
        $message = $request->message;
        $limit_amount = $this->limit_amount;

        if($request->id > 0){
            try
                {
                    $input2 = self::getHistory($request->id);

                    foreach($input2 as $a => $input){
                        
                        $token_id = Str::random(32);
                        $form = Form::where('id', $input['form_id'])->update(['balance' => 1, 'token' => $token_id]);
                        $token = Config('app.url').'spinner/form/'.$token_id;
                        $form = [
                            'name' => $input['full_name'],
                            'message' => $message,
                            'number' => $input['number'],
                            'message-end' => '',
                            'limit_amount' => $limit_amount,
                            'load' => $input['totals']['load'],
                            'type' => $type,
                            'subject' => '',
                            'token_id' => $token_id,
                            'load-remaining' => 0
                        ];
                    $sendtext = 'Hello '.$form['name'].','.$form['message'].' ';
                    
                    $settings = GeneralSetting::where('id',1)->first()->toArray();
                    $key = (string) $settings['api_key'];
                    $secret = (string) $settings['api_secret'];
                    
                    $basic  = new \Vonage\Client\Credentials\Basic($key, $secret);
                    $client = new \Vonage\Client($basic);
                    
                    if($type == 'above-'.$limit_amount){
                        if($form['load']  >= $limit_amount){
                             $form['subject'] = 'Noor Games - Eligible For Spinner';
                             $sendtext .= 'You have loaded a total of '.$form['load'].'Your spinner link is '.$token;
                         }
                     }elseif($type == 'below-'.$limit_amount){
                         if($form['load']  <  $limit_amount){
                             $form['subject'] = 'Noor Games - Spinner';
                             // $form['message-end'] = 'Noor Games - Eligible For Spinner';              
                             $form['load-remaining'] = $limit_amount - $form['load'];
                             $sendtext .= 'Only '.$form['load-remaining'].' left to be eligible for the spinner.';
                         }
                     }else{
                         $limit_1 = $limit_amount - 50;
                         $limit_2 = $limit_amount;
                         if($form['load']  >= $limit_1){
                             if($form['load']  < $limit_2){  
                                 $form['subject'] = 'Noor Games - Almost Eligible For Spinner';                    
                                 $form['load-remaining'] = $limit_amount - $form['load'];
                                 $sendtext .= 'Only '.$form['load-remaining'].' left to be eligible for the spinner.';
                             }
                         }
                     }

                    }
                    try
                    {
                        $message = $client->message()->send([
                        'to' => $form['number'],
                        'from' => '18337222376',
                        'text' => $sendtext
                        ]);
                        Log::channel('spinnerBulk')->info("SMS sent successfully to ".$input['number']);
                        return redirect()->back()->withInput()->with('success', 'SMS Sent');
                    }
                    catch(\Exception $e)
                    {
                        $bug = $e->getMessage();
                        Log::channel('spinnerBulk')->info($bug);
                        return redirect()->back()->withInput()->with('error', $bug);
                    }
                    return redirect()->back()->withInput()->with('success', 'SMS Sent');
                }
            catch(\Exception $e)
                {
                    $bug = $e->getMessage();
                    Log::channel('spinnerBulk')->info($bug);
                    return redirect()->back()->withInput()->with('error', $bug);
                }
        }
    }
    public function gamerGames($id)
    {
        $type = $id;

        if($type == 'all'){

            $history = History::with('account')->with('form')
                ->whereHas('form')
                ->with('created_by')
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
            $final = [];
            $forms = [];

            // $data = [
            //     ['SN', 'Date', 'FB Name','Game','Game ID','Amount','Type','Creator']
            // ];
            if (!empty($history))
            {
                foreach ($history as $a => $b)
                {
                    $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
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
                            $final[$b['form_id']]['spinner_key'] = $form['token'];
                            $final[$b['form_id']]['form_id'] = $b['form_id'];
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
                        // dd($totals);
                        // array_push($final,$b);
                        // array_push($forms,$b['form']);
                        
                    }
                }
            }
            $forms = $final;
            // dd($final);
            $limit_amount = $this->limit_amount;
            $filter_start = 'all';
            $filter_end = 'all';
        }
        else{
            $month = date('m');
            if($month >10){
                $month = '0'.$month;
            }
            $filter_start = '2022-'.$month.'-01';
            $filter_end = Carbon::now();
            $history = History::with('account')->with('form')
            ->whereHas('form')
            ->whereBetween('created_at',[date($filter_start),date($filter_end)])
            ->with('created_by')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

            $final = [];
            $forms = [];

            // $data = [
            //     ['SN', 'Date', 'FB Name','Game','Game ID','Amount','Type','Creator']
            // ];
            if (!empty($history))
            {
                foreach ($history as $a => $b)
                {
                    $totals = ['tip' => 0, 'load' => 0, 'redeem' => 0, 'refer' => 0, 'cashAppLoad' => 0];
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
                        // dd($totals);
                        // array_push($final,$b);
                        // array_push($forms,$b['form']);
                        
                    }
                }
            }
            // dd($type);
            $limit = 0;
            $final_2 = [];
            if(!empty($final)){
                foreach ($final as $a => $b){
                    if($type == 'above-'.$this->limit_amount){
                        if($b['totals']['load']  >= $this->limit_amount){
                            array_push($final_2,$b);
                        }
                    }elseif($type == 'below-'.$this->limit_amount){
                        if($b['totals']['load']  <  $this->limit_amount){
                            array_push($final_2,$b);
                        }
                    }else{
                        $limit_1 = $this->limit_amount - 50;
                        $limit_2 = $this->limit_amount;
                        if($b['totals']['load']  >= $limit_1){
                            if($b['totals']['load']  < $limit_2){
                                array_push($final_2,$b);    
                            }
                        }
                    }
                }
            }
            $forms = $final_2;
            $limit_amount = $this->limit_amount;

        }
        return view('newLayout.gamers-games', compact('forms','limit_amount','filter_start','filter_end'));
    }
    public function settings(){
        $settings = GeneralSetting::first()->toArray();
        return view('newLayout.settings',compact('settings'));
    }
    public function settingStore(Request $request){
         try
        {
            $settings = GeneralSetting::where('id',1)->update([
                'bonus_report_emails' => ($request->bonus_report_emails[0] != null)?$request->bonus_report_emails[0]:null,
                'new_register_mail' => ($request->new_register_mail[0] != null)?$request->new_register_mail[0]:null,
                'emails' => ($request->emails[0] != null)?$request->emails[0]:null,
                'limit_amount' => $request->limit_amount,
                'spinner_message_monthly' => $request->spinner_message_monthly,
                'spinner_message' => $request->spinner_message,
                'above_limit_text' => $request->above_limit_text,
                'between_limit_text' => $request->between_limit_text,
                'below_limit_text' => $request->below_limit_text,
                'captcha' => $request->captcha,
                'captcha_type' => $request->captcha_type,
                'api_key' => $request->api_key,
                'api_secret' => $request->api_secret,
                'theme' => $request->theme,
                'registration_email' => $request->registration_email,
                'registration_sms' => $request->registration_sms,
                'mail_text' => $request->mail_text,
                'sms_text' => $request->sms_text
            ]);
            // $settings = GeneralSetting::first()->toArray();
            return redirect()->back()->withInput()->with('success', 'Settings Updated');
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error', $bug);
        }
    }
    public function userDetails($id){
        $form_id = $id;
        $form_games = FormGame::where('form_id',$form_id)->with('account')->get()->toArray();   
        $form = Form::where('id',$form_id)->first()->toArray();   
        // dd($form_games);
        return view('newLayout.userDetails', compact('form_games','form'));    

    }
    public function updateGameId(Request $request){
        $form_id = $request->cid;
        $accountId = $request->accountId;
        $gameId = $request->gameId;

        try
        {
            $form_games = FormGame::where(['form_id' => $form_id,'account_id' => $accountId])->update(['game_id' => $gameId]);   
            return Response::json(true);
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return Response::json(['error' => $bug], 404);
        } 
    }
    public function gameids(Request $request){
        $form_id = $request->userId;
        $form_games = FormGame::where('form_id',$form_id)->with('account')->get()->toArray();        
        return Response::json($form_games);

    }
    public function gameData(){
        $year = isset($_GET['year']) ? $_GET['year'] : '';
        $month = isset($_GET['month']) ? $_GET['month'] : '';
        $day = isset($_GET['day']) ? $_GET['day'] : '';

        if (empty($year))
        {
            $year = date('Y');
        }
        if (empty($month))
        {
            $month = date('m');
        }
        if (empty($day))
        {           
            // echo 's';
        $history = History::whereDate('created_at', '>=', date($year.'-'.$month.'-01'))
                            ->whereDate('created_at', '<=', date($year.'-'.$month.'-31'))
                            ->count();
        }else{
            // echo 'no';
            $history = History::whereDate('created_at', '>=', date($year . '-' . $month . '-'.$day))
            ->whereDate('created_at', '<=', date($year . '-' . $month . '-'.$day))
            ->count();  
        }
        // dd($month,$history,date($year.'-'.$month.'-01'),date($year.'-'.$month.'-32'));

        $grouped = [];
        if (($history > 0)){
            if (empty($day))
            {           
                $history = History::whereHas('form')
                                ->whereDate('created_at', '>=', date($year . '-' . $month . '-01'))
                                ->whereDate('created_at', '<=', date($year . '-' . $month . '-31'))
                                ->orderBy('created_at', 'asc')
                                ->with('account')
                                ->whereHas('account')
                                    ->get()
                                    ->toArray();
            }else{
                $history = History::whereHas('form')
                                ->whereDate('created_at', '>=', date($year . '-' . $month . '-'.$day))
                                ->whereDate('created_at', '<=', date($year . '-' . $month . '-'.$day))
                                ->orderBy('created_at', 'asc')
                                ->with('account')
                                ->whereHas('account')
                                    ->get()
                                    ->toArray();
            }
            // dd($history);
            $totals = [
                'tip' => 0,
                'load' => 0,
                'redeem' => 0,
                'refer' => 0,
                'cashAppLoad' => 0,
            ];
            foreach ($history as $a => $b) {
                $created_at = explode('-', date('Y-m-d', strtotime($b['created_at'])));
                if (!(isset($grouped[$created_at[2]][$b['account_id']])))
                {
                    $grouped[$created_at[2]][$b['account_id']] = 
                    [
                        'game_id' => $b['account']['id'], 
                        'game_name' => $b['account']['name'], 
                        'game_title' => $b['account']['title'], 
                        'game_balance' => $b['account']['balance'], 
                        'histories' => [], 
                        'totals' => $totals, 
                        'total_transactions' => 0
                    ];
                }

                ($b['type'] == 'tip') ? ($grouped[$created_at[2]][$b['account_id']]['totals']['tip'] = $grouped[$created_at[2]][$b['account_id']]['totals']['tip'] + $b['amount_loaded']) : ($grouped[$created_at[2]][$b['account_id']]['totals']['tip'] = $grouped[$created_at[2]][$b['account_id']]['totals']['tip']);
                ($b['type'] == 'load') ? ($grouped[$created_at[2]][$b['account_id']]['totals']['load'] = $grouped[$created_at[2]][$b['account_id']]['totals']['load'] + $b['amount_loaded']) : ($grouped[$created_at[2]][$b['account_id']]['totals']['load'] = $grouped[$created_at[2]][$b['account_id']]['totals']['load']);
                ($b['type'] == 'redeem') ? ($grouped[$created_at[2]][$b['account_id']]['totals']['redeem'] = $grouped[$created_at[2]][$b['account_id']]['totals']['redeem'] + $b['amount_loaded']) : ($grouped[$created_at[2]][$b['account_id']]['totals']['redeem'] = $grouped[$created_at[2]][$b['account_id']]['totals']['redeem']);
                ($b['type'] == 'refer') ? ($grouped[$created_at[2]][$b['account_id']]['totals']['refer'] = $grouped[$created_at[2]][$b['account_id']]['totals']['refer'] + $b['amount_loaded']) : ($grouped[$created_at[2]][$b['account_id']]['totals']['refer'] = $grouped[$created_at[2]][$b['account_id']]['totals']['refer']);
                ($b['type'] == 'cashAppLoad') ? ($grouped[$created_at[2]][$b['account_id']]['totals']['cashAppLoad'] = $grouped[$created_at[2]][$b['account_id']]['totals']['cashAppLoad'] + $b['amount_loaded']) : ($grouped[$created_at[2]][$b['account_id']]['totals']['cashAppLoad'] = $grouped[$created_at[2]][$b['account_id']]['totals']['cashAppLoad']);
                
                $grouped[$created_at[2]][$b['account_id']]['total_transactions'] = $grouped[$created_at[2]][$b['account_id']]['total_transactions'] + 1;
                array_push($grouped[$created_at[2]][$b['account_id']]['histories'], $b);
            }
        }else{
            
        }
        $all_months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
    //  dd($month);
        return view('newLayout.gameData', compact('grouped', 'month', 'year','day','all_months'));
            //  dd($grouped);
    }

      public function addHistory(Request $request){
        try
        {
            if(isset($request->yesterday) && $request->yesterday == 1){
                $history = History::create([
                    'created_at' => Carbon::yesterday(),
                    'form_id' => $request->id,
                    'account_id' => $request->account,
                    'amount_loaded' => $request->amount_loaded,
                    'type' => $request->type,
                    'created_by' => Auth::user()->id
                ]);
            }else{
                $history = History::create([
                    'created_at' => $request->date,
                    'form_id' => $request->id,
                    'account_id' => $request->account,
                    'amount_loaded' => $request->amount_loaded,
                    'type' => $request->type,
                    'created_by' => Auth::user()->id
                ]);
            }
            return redirect()->back()->withInput()->with('success', 'History Added');
        }
        catch(\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error', $bug);
        }
    }
}

