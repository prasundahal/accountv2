<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\FrontSetting;
use App\Models\Account;
use App\Models\CashierFrontend;
use Auth;
use Carbon\Carbon;
use App\Models\LoginLog;

class AdminController extends Controller
{
    public function index()
    {
        $front_setting = FrontSetting::first();
        return view('admin.dashboard');
    }

    public function login()
    {
        $front_setting = FrontSetting::first();
        return view('admin.login',compact('front_setting'));
    }
    public function getCredential()
    {
        return view('admin.pages.credential');
    }
    public function getFrontend()
    {
        return view("admin.pages.frontend_setting");
    }
   public function showGames()
   {
        $account = Account::all();
       return view('admin.pages.games.index',compact('account'));
   }

   public function storeAccount(Request $request)
   {
    return view('admin.pages.games.create');
   }

   public function storeGames(Request $request)
   {
       $account = new Account;
       $account->title = strtoupper($request->title);
       $account->balance = $request->balance;
       $account->status = 1;
       $account->save();
       return redirect()->route('admin.games')->with('success','Game has been added');
   }

   public function editGames($id)
   {
       $account = Account::find($id);
       return view('admin.pages.games.edit',compact('account'));
   }

   public function updateGames(Request $request,$id)
   {
    $account = Account::find($id);
    $account->title = strtoupper($request->title);
    $account->balance = $request->balance;
    $account->save();
    return redirect()->route('admin.games')->with('success','Game has been updated');
   }

   public function delGames($id)
   {
       $account = Account::find($id);
       $account->delete();
       return redirect()->route('admin.games')->with('success','Game has been deleted');

   }

   public function trashGames()
   {
       $account = Account::onlyTrashed()->get();
       return view('admin.pages.games.game_trash',compact('account'));
   }

   public function restoreGames($id)
   {
       $account = Account::withTrashed()->find($id);
       if(!is_null($account))
       {
        $account->restore();
       }       
       return redirect()->route('admin.games')->with('success','Game has been deleted');

   }

   public function forceDelGames($id)
   {
       $account = Account::withTrashed()->find($id);
       if(!is_null($account))
       {
        $account->forceDelete();
       }       
       return redirect()->route('admin.games')->with('success','Game has been deleted');

   }


   public function updateStatus(Request $request)
   {
       
       $account = Account::find($request->id);
       $account->status = $request->status;
       if($account->status == 1){
           if(count(Account::where('status', 1)->get()) < 4)
           {
               if($account->save()){
                   return 'active';
               }
               else {
                   return 'inactive';
               }
           }
       }
       else{
           if($account->save()){
               return 'active';
           }
           else {
               return 'inactive';
           }
       }

       return '0';
   }

   public function showCashierFrontSetting()
   {
       $front_setting = CashierFrontend::all();
       return view('admin.pages.cashier-front',compact('front_setting'));
   }

   public function storeCashierFront(Request $request)
   {
    $frontSetting = CashierFrontend::first();

    if($request->hasFile('logo')){
        $frontSetting->logo = $request->file('logo')->store('uploads/cashier_logo');
    }

    if($request->hasFile('cashier_logo')){
        $frontSetting->cashier_logo = $request->file('cashier_logo')->store('uploads/cashier_logoo');
    }

    if($request->hasFile('cashier_favicon')){
        $frontSetting->cashier_favicon = $request->file('cashier_favicon')->store('uploads/cashier_favicon');
    }

    if($request->hasFile('cashier_login_background')){
        $frontSetting->cashier_background = $request->file('cashier_login_background')->store('uploads/cashier__login_background');
    }

    if($request->hasFile('cashier_login_sidebar')){
        // $imgName = time().'.'.$request->admin_login_sidebar->extension();
        // $frontSetting->admin_login_sidebar = $request->admin_login_sidebar->move(public_path('/buploads/admin_login_sidebar'),$imgName);
         $frontSetting->cashier_login_sidebar = $request->file('cashier_login_sidebar')->store('uploads/cashier__login_sidebar');
    }

    if($frontSetting->save()){
        return redirect()->route('admin.index');
    }
    else{
        return back();
    }
   }

    public function logout()
    {
        Session::flush();
        $id = session()->get('log_id');
        if(!empty($id)){
            $save_log = LoginLog::where('id',$id)->update(['logout_time' => Carbon::now()]);
        }
        Auth::logout();
        return redirect('login');
    }
}
