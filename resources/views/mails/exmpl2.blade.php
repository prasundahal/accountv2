@component('mail::message')
   @if(isset($details) && !empty($details))
  
   @php
   $details = json_decode($details,'true');
   $token = 'https://test.noorgames.net/spinner/form/'.$details['token'];
   // dd($details);
   // print_r($details['token']);
   @endphp
      Congratulations !! <br>
      You have loaded enough balance to be eligible for spinner lottery.
      <br>
      Click this link below and fill up the form to go to further process.
      <br>
      @foreach($details as $link => $a)
         @if ($link == 'token')
         {{$token}}
            {{-- <a href="{{$token}}">This is a link</a> --}}
         @endif
      @endforeach
      {{-- <a href="'https://test.noorgames.net/spinner/form/'.$token" class="ams bkG">Click me</a> --}}
      {{-- @component('mail::button', ['url' =>$token])
         Click me
      @endcomponent --}}
   @endif
  <br>
       
        
        Sincerely,
        Noor Games.
@endcomponent
