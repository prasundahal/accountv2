@extends('newLayout.layouts.newLayout')

@section('title')
    Settings
@endsection

@section('content')
{{-- @php
    dd($limit_amount);
@endphp --}}
<style>
    .example-text{
        padding: 5px;
        background: #e9ecef;
    }
    .bootstrap-tagsinput{
        padding: 10px 10px;
    }
    .tag{
        background: #fbb244;
        padding: 5px;
    }
</style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <form action="{{ route('settingStore') }}" method="POST">
                    <div class="card">
                        {{-- <div class="card-header">{{ __('Edit Noorgamers') }}</div> --}}
                        <div class="card-body">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-lg-4 col-sm-12">
                                        <span>Limit Amount</span>
                                        <input type="number" class="form-control" name="limit_amount" value="{{$settings['limit_amount']}}" >
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <span>Spinner Message Monthly</span>
                                        <select name="spinner_message_monthly" class="form-control">
                                            <option {{($settings['spinner_message_monthly']==1)?'selected':''}} value="1">On</option>
                                            <option {{($settings['spinner_message_monthly']==0)?'selected':''}} value="0">Off</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <span>Spinner Message</span>
                                        <select name="spinner_message" class="form-control">
                                            <option {{($settings['spinner_message']==1)?'selected':''}} value="1">On</option>
                                            <option {{($settings['spinner_message']==0)?'selected':''}} value="0">Off</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <span>Registration Email</span>
                                        <select name="registration_email" class="form-control">
                                            <option {{($settings['registration_email']==1)?'selected':''}} value="1">On</option>
                                            <option {{($settings['registration_email']==0)?'selected':''}} value="0">Off</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-sm-12">
                                        <span>Registration SMS</span>
                                        <select name="registration_sms" class="form-control">
                                            <option {{($settings['registration_sms']==1)?'selected':''}} value="1">On</option>
                                            <option {{($settings['registration_sms']==0)?'selected':''}} value="0">Off</option>
                                        </select>
                                    </div>
                                </div>
                                </br>
                                <div class="row">
                                    <div class="col" style="display: inline-grid;">
                                        <span>Send Bonus Emails To</span>
                                        <input type="text" class="form-control emails" name="bonus_report_emails[]" value="{{$settings['bonus_report_emails']}}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col" style="display: inline-grid;">
                                        <span>Send 24 hour Report Emails To</span>
                                        <input type="text" class="form-control emails" name="emails[]" value="{{$settings['emails']}}" >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col" style="display: inline-grid;">
                                        <span>Send New Registration Email To</span>
                                        <input type="text" class="form-control emails" name="new_register_mail[]" value="{{$settings['new_register_mail']}}" >
                                    </div>
                                </div>
                                </br>
                                </br></br>
                        </div>
                    </div>
                    <div class="card mt-5">
                        <div class="card-body">
                            
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>Above Limit Text </span>
                                    <textarea name="above_limit_text" id="" class="form-control above-limit-text">{{$settings['above_limit_text']}}</textarea>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>Above Limit Text Example</span>
                                    <div class="example-text">
                                        <p class="mb-0">Hello {Name}</p>
                                        <p class="mb-0 above-example-text">{{$settings['above_limit_text']}}</p>
                                        <p class="mb-0">You have loaded a total of {Load Amount}</p>
                                        <p class="mb-0">Your spinner link is {Token}</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>Between Limit Text </span>
                                    <textarea name="between_limit_text" id="" class="form-control between-limit-text">{{$settings['between_limit_text']}}</textarea>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>Between Limit Text Example</span>
                                    <div class="example-text">
                                        <p class="mb-0">Hello {Name}</p>
                                        <p class="mb-0 between-example-text">{{$settings['between_limit_text']}}</p>
                                        <p class="mb-0">Only {Remaining Balance} left to be eligible for the spinner.</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>Below Limit Text </span>
                                    <textarea name="below_limit_text" id="" class="form-control below-limit-text">{{$settings['below_limit_text']}}</textarea>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>Below Limit Text Example</span>
                                    <div class="example-text">
                                        <p class="mb-0">Hello {Name}</p>
                                        <p class="mb-0 below-example-text">{{$settings['below_limit_text']}}</p>
                                        <p class="mb-0">Only {Remaining Balance} left to be eligible for the spinner.</p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>New Registration Admin Mail Text </span>
                                    <textarea name="mail_text" id="" class="form-control mail_text-text">{{$settings['mail_text']}}</textarea>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>New Registration Admin Mail Text Example</span>
                                    <div class="example-text">
                                        <p class="mb-0">Hello Admin, {Users Name} <span class="mail_text-example-text">{{$settings['mail_text']}}</span> </p>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>New Registration User Text </span>
                                    <textarea name="sms_text" id="" class="form-control sms_text-text">{{$settings['sms_text']}}</textarea>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                                    <span>New Registration User Text Example</span>
                                    <div class="example-text">
                                        <p class="mb-0">Congratulations {Users Name}.Welcome to {Game Name} Gamers Club.<span class="sms_text-example-text">{{$settings['sms_text']}}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-5">
                        {{-- <div class="card-header">{{ __('Edit Noorgamers') }}</div> --}}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <span>Captcha</span>
                                    <select name="captcha" class="form-control">
                                        <option {{($settings['captcha']==1)?'selected':''}} value="1">On</option>
                                        <option {{($settings['captcha']==0)?'selected':''}} value="0">Off</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <span>Captcha Type</span>
                                    <select name="captcha_type" class="form-control">
                                        <option {{($settings['captcha_type']=='custom')?'selected':''}} value="custom">Custom</option>
                                        <option {{($settings['captcha_type']=='google')?'selected':''}} value="google">Google</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <span>SMS Key</span>
                                    <input type="text" class="form-control" name="api_key" value="{{$settings['api_key']}}" >
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <span>SMS Secret</span>
                                    <input type="text" class="form-control" name="api_secret" value="{{$settings['api_secret']}}" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-5">
                        {{-- <div class="card-header">
                            <h3>Themes</h3>
                        </div> --}}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <span>Theme</span>
                                    <select name="theme" class="form-control">
                                        <option {{($settings['theme']=='default')?'selected':''}} value="default">Default</option>
                                        <option {{($settings['theme']=='anna')?'selected':''}} value="anna">Anna</option>
                                    </select>
                                </div>
                                <div class="col-lg-12 col-sm-12">
                                    <button type="submit" class="btn btn-primary mt-2">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

                    @endsection

                    @section('script')
                        <script>
                            $('.above-limit-text').on('keyup',function(){
                                var val = $(this).val();
                                $('.above-example-text').text(val);
                            });
                            $('.between-limit-text').on('keyup',function(){
                                var val = $(this).val();
                                $('.between-example-text').text(val);
                            });
                            $('.below-limit-text').on('keyup',function(){
                                var val = $(this).val();
                                $('.below-example-text').text(val);
                            });
                            $('.mail_text-text').on('keyup',function(){
                                var val = $(this).val();
                                $('.mail_text-example-text').text(val);
                            });
                            $('.sms_text-text').on('keyup',function(){
                                var val = $(this).val();
                                $('.sms_text-example-text').text(val);
                            });
                        </script>
                    @endsection
