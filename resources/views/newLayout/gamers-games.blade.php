@extends('newLayout.layouts.newLayout')

@section('title')
    Gamer & Games
@endsection
@section('content')
@php
    use Carbon\Carbon;
    $settings = \App\Models\GeneralSetting::first()->toArray();
@endphp
<style>
       td{
            border: none;
        }
    .count-row{
        font-weight: 700;
    }
    

.overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  transition: opacity 500ms;
  visibility: hidden;
  opacity: 0;
  z-index: 9999;
}
.overlay:target {
  visibility: visible;
  opacity: 1;
}


.popup {
  margin: 70px auto;
  padding: 20px;
  background: #fff;
  border-radius: 5px;
  width: 50% ;
  position: relative;
  transition: all 5s ease-in-out;
 
  

}

.popup h2 {
  margin-top: 0;
  color: #333;
  font-family: Tahoma, Arial, sans-serif;

}
.popup .close {
  position: absolute;
  top: 20px;
  right: 30px;
  transition: all 200ms;
  font-size: 30px;
  font-weight: bold;
  text-decoration: none;
  color: #333;

}
.popup .close:hover {
  color: #FF9800;
}
.popup .content {
  max-height: calc(100vh - 210px);
    overflow-y: auto;
}

@media screen and (max-width: 700px){
  .box{
    width: 70%;
  }
  .popup{
    width: 70%;
  }
}


</style>
<div class="row justify-content-center">
    <div class="col-12 card mb-5">
        <div class="card-body">
            <select class="form-control" name="" id="inactive-players-select">
                <option value="all" {{((request()->segment(2) == 'all'))?'selected':''}}>All</option>
                <option value="above-{{$limit_amount}}" {{((request()->segment(2) == 'above-'.$limit_amount))?'selected':''}}>Balance Loaded Over {{$limit_amount}}</option>
                <option value="between" {{((request()->segment(2) == 'between'))?'selected':''}}>Balance Loaded Between {{($limit_amount - 50)}} && {{($limit_amount)}}</option>
                <option value="below-{{($limit_amount)}}" {{((request()->segment(2) == 'below-'.$limit_amount))?'selected':''}}>Balance Loaded Below {{($limit_amount)}}</option>
            </select>
            
            @if ($spinner_message == 1)
              @if ($filter_start != 'all')
                  <button  class="btn  btn-primary mb-0" style="background-color:#FF9800;"  > 
                    <a href="#popup3" style="color:white;">Send Mail</a></button>
                  <div id="popup3" class="overlay" style="z-index: 9;">
                    <div class="popup">
                        <h2>Send Mail</h2>
                        <a class="close" href="#">&times;</a>
                        <div class="content ">
                          <form action="{{route('send-message')}}" method="post">
                              @csrf
                              <input name="id" type="hidden" value="0" class="form-id">
                              <input name="type" type="hidden" value="{{request()->segment(2)}}">
                          <div class="row">
                              <div class="form-group">
                                <label for="cars">Message</label>
                                  @if ((request()->segment(2) == 'above-'.$limit_amount))
                                      <textarea name="message" id="cars" class="form-control" placeholder="write your message">{{$settings['above_limit_text']}}</textarea>                                  
                                  @elseif((request()->segment(2) == 'between'))
                                      <textarea name="message" id="cars" class="form-control" placeholder="write your message">{{$settings['between_limit_text']}}</textarea>                                  
                                  @else
                                      <textarea name="message" id="cars" class="form-control" placeholder="write your message">{{$settings['below_limit_text']}}</textarea>                                  
                                  @endif
                              </div>
                              <button type="submit"  class="btn  btn-primary mb-0" style="background-color:#FF9800;"  >Send</button>
                        
                          </div>
                        </form>
                          </div>
                    </div>
                  </div>
                  <p>This Data is  Between {{$filter_start}} && {{$filter_end}}</p>            
              @endif         
            @endif
        </div>
    </div>
            <div class="col-md-12 card">
                <div class="table-responsive p-4" style="overflow-x:auto;">
                     <!--id="datatable-crud"-->
                    <table class="table datatable" style="font-size:14px">
                        <thead>
                            <tr>
                                    <th style="width: 26.328100000000006px!important;">No</th>
                                    @if ($filter_start != 'all')
                                      @if ($spinner_message == 1)
                                        <th class="cus-width">Mail</th>
                                        <th class="cus-width">SMS</th>
                                      @endif
                                    @endif
                                    <th class="cus-width">Full Name</th>
                                    <th class="cus-width">Number</th>
                                    <th class="cus-width">Email</th>
                                    <th class="cus-width">Fb ID</th>
                                  
                                    <th class="cus-width">Balance Load</th>
                                    <th class="cus-width">Spiner key</th>
                                    
                            </tr>
                        </thead>
                         <tbody>
                   @php
                    $count = 1;
                   @endphp
                  @foreach($forms as $num)
                  <tr class="tr-{{$num['form_id']}}">
                    <td class="count-row">{{$count++}}</th>
                    @if ($filter_start != 'all')
                      @if ($spinner_message == 1)
                        <td class="td-message-{{$num['form_id']}}">
                          <a href="#popup3" class="btn btn-primary send-message-single" data-id="{{$num['form_id']}}">Send</a>
                        </td>
                        <td class="td-message-{{$num['form_id']}}">
                          <a href="#popup4" class="btn btn-secondary send-sms-single" data-id="{{$num['form_id']}}">Send</a>
                        </td>
                        <div id="popup4" class="overlay" style="z-index: 9;">
                          <div class="popup">
                              <h2>Send Mail</h2>
                              <a class="close" href="#">&times;</a>
                              <div class="content ">
                                <form action="{{route('send-sms')}}" method="post">
                                    @csrf
                                    <input name="id" type="hidden" value="0" class="form-id">
                                    <input name="type" type="hidden" value="{{request()->segment(2)}}">
                                <div class="row">
                                    <div class="form-group">
                                      <label for="cars">Message</label>
                                        @if ((request()->segment(2) == 'above-'.$limit_amount))
                                            <textarea name="message" id="cars" class="form-control" placeholder="write your message">{{$settings['above_limit_text']}}</textarea>                                  
                                        @elseif((request()->segment(2) == 'between'))
                                            <textarea name="message" id="cars" class="form-control" placeholder="write your message">{{$settings['between_limit_text']}}</textarea>                                  
                                        @else
                                            <textarea name="message" id="cars" class="form-control" placeholder="write your message">{{$settings['below_limit_text']}}</textarea>                                  
                                        @endif
                                    </div>
                                    <button type="submit"  class="btn  btn-primary mb-0" style="background-color:#FF9800;"  >Send</button>
                              
                                </div>
                              </form>
                                </div>
                          </div>
                        </div>
                      @endif
                    @endif

                    <td class="td-full_name-{{$num['form_id']}}">{{ucwords($num['full_name'])}}</td>
                    <td class="class td-game_id-{{$num['form_id']}}" data-id="{{$num['form_id']}}" data-type="number">{{($num['number'])}}</td>
                    <td class="class td-game_id-{{$num['form_id']}}" data-id="{{$num['form_id']}}" data-type="email">{{($num['email'])}}</td>
                    <td class="td-facebook_name-{{$num['form_id']}}">{{($num['facebook_name'])}}</td>
                 
                    <td class="td-load-{{$num['form_id']}}">{{($num['totals']['load'])}}</td>
                    <td class="td-load-{{$num['form_id']}}">
                      {{$num['spinner_key']}}
                    </td>

                  </tr>
                  @endforeach
               </tbody>
                    </table>
                </div>

                </div>
            </div>
    </div>
@endsection
@section('script')
<script>
    $(document).on('change','#inactive-players-select',function(){
        // console.log($(this).val());
        window.location.replace('/gamers-games/'+$(this).val());
    });

    $('.send-message-single').on('click',function(){
      var id = $(this).data('id');
      $('.form-id').val(id);
    });
    $('.send-sms-single').on('click',function(){
      var id = $(this).data('id');
      $('.form-id').val(id);
    });
    $(document).ready( function () {
       $('table').editableTableWidget();
       
       $('table td').on('change', function(evt, newValue) {
        var type = "POST";
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        
             $.ajax({
                type: type,
                url: '/updateForm',
                data: {
                    "cid": $(this).data('id'),
                    "type": $(this).data('type'),
                    "note" : newValue
                },
                dataType: 'json',
                success: function (data) {
                    toastr.success('Success',"Updated");
                    
                },
                error: function (data) {
                    toastr.error('Error',data.responseText);
                }
            });
           });
    });
</script>
@endsection

