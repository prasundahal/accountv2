<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormNumber;
use Illuminate\Support\Facades\Validator;
use Response;

class FormNumberConroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
       public function __construct() {
        $this->middleware('auth', ['except' => [
            'store','go'
        ]]);
    }
      public function go($x)
    {
        return redirect()->away('http://firekirin.xyz:8580/index.html');
        dd($x,'http://firekirin.xyz:8580/index.html');
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number',
            'name' => 'required'
        ]);
        // $request->validate([
        // 'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number'
        // ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        $formdata = array(
            'cash_app_send_limit'       =>   isset($request->cash_app_send_limit)?($request->cash_app_send_limit):null,
            'us_citizen'       =>   isset($request->us_citizen)?($request->us_citizen):null,
            'cash_app'       =>   isset($request->cash_app)?($request->cash_app):null,
            'driving_license'       =>   isset($request->driving_license)?($request->driving_license):null,
            'cash_app_score'       =>   isset($request->cash_app_score)?($request->cash_app_score):null,
            'cash_app_send_limit'       =>   isset($request->cash_app_send_limit)?($request->cash_app_send_limit):null,
            'state'       =>   isset($request->state)?($request->state):null,
            'crime'       =>   isset($request->crime)?($request->crime):null,
            'extra_1'       =>   isset($request->extra_1)?($request->extra_1):null,
            'extra_2'       =>   isset($request->name)?($request->name):null,
            'phone_number'       =>   isset($request->phone_number)?($request->phone_number):null
        
        );
        $sql = FormNumber::create($formdata);  
        if(!$sql){
            return redirect()->back()->withInput()->with('error', $sql);
        }
      
        $sendtexttouser ='Welcome'.' ' . $request->name . ' ' .', to Noor Games family.We have received your details. Someone from collab team will reach back to you based on your eligibility.
           Note: Don not  bother asking to Sasha when will they reach out.';
        $str = $request->phone_number;
        $usernumber = preg_replace('/[^0-9]/','',$str);
        $usernumberint = (int) filter_var($usernumber, FILTER_SANITIZE_NUMBER_INT);
//  dd($usernumberint);
        $basic  = new \Vonage\Client\Credentials\Basic("e20bd554", "M5arJoXIrJ8Kat1r");
        $client = new \Vonage\Client($basic);
        $message = $client->message()->send([
        'to' => '1'.$usernumber,
        'from' => '18337222376',
        'text' => $sendtexttouser
        ]);
       
        
          $sendtexttoadmin =' Collab team      '.' '  . $request->name . ' is added to the family with ' . 'Phone ' . $request->phone_number . ' ' . 'Take your time to reach out to him.';
        // dd($sendtexttoadmin, $sendtexttouser);
        $basic  = new \Vonage\Client\Credentials\Basic("e20bd554", "M5arJoXIrJ8Kat1r");
        $client = new \Vonage\Client($basic);
        $message = $client->message()->send([
        'to' => '19292684435',
        'from' => '18337222376',
        'text' => $sendtexttoadmin
        ]);
        
        
        return redirect('formsuccess')->with('success', 'Thank You. ');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $form = FormNumber::find($id);
       return view('colabEdit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number',
            'extra_2' => 'required'
        ]);
        // $request->validate([
        // 'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:forms,number'
        // ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        $formdata = array(
            'note'       =>   isset($request->note)?($request->note):null,
            'extra_2'       =>   isset($request->extra_2)?($request->extra_2):null,
            'phone_number'       =>   isset($request->phone_number)?($request->phone_number):null
        
        );
        $sql = FormNumber::find($request->id);  
        $sql->note = isset($request->note)?($request->note):null;
        $sql->extra_2 = isset($request->extra_2)?($request->extra_2):null;
        $sql->phone_number = isset($request->phone_number)?($request->phone_number):null;
        if(!$sql->save()){
            return redirect()->back()->withInput()->with('error', $sql);
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
         return redirect()->route('colab')->with('success', 'Updated');
            // return redirect()->route('colab.edit',['id' => $request->id])->with('success', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sql = FormNumber::find($id)->delete();
        // dd('asdf');
        return redirect()->route('colab');
    }
    
      public function saveNote(Request $request)
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
            'note'       =>   isset($request->note)?($request->note):null,
        );
        $sql = FormNumber::find($request->cid);  
            // return Response::json($sql);
        $sql->note = isset($request->note)?($request->note):null;
        if(!$sql->save()){
            return Response::json(['error' => $sql],404);
            // return redirect()->back()->withInput()->with('error', $sql);
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
            return Response::json('Note Saved');
        //  return redirect()->route('colab')->with('success', 'Updated');
            // return redirect()->route('colab.edit',['id' => $request->id])->with('success', 'Updated');
    }
}
