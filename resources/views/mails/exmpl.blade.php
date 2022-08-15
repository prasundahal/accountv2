@component('mail::message')
@php
$details = json_decode($details,'true');
@endphp
          @if(isset($details) && !empty($details['forms']))
        Hello Admin, These are the list of users who will get bonous today -- <?php echo Carbon\Carbon::now().'   ('.config('app.timezone').')' ?>.
        <table  width="800px!important;" border="0" cellspacing="0" cellpadding="0">
          <tr style="background: #001fff;color: #ffff;">
             <td  style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" width="5%" height="32" align="center">SN</td>
             <td  style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;" width="25%" align="center">Name</td> 
               <!--<td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;" width="25%" align="center">Number</td>-->
               <!--<td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;" width="10%" align="center">Email</td>-->
               <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333;" width="5%" align="center">Facebook Name</td>
               <td style="font-family:Verdana, Geneva, sans-serif; font-weight:600; font-size:13px; border-top:1px solid #333; border-bottom:1px solid #333; border-right:1px solid #333; border-right:1px solid #333;" width="15%" align="center">Game Id</td>
          </tr>
          @foreach ($details['forms'] as $key => $orderDetail)
          <tbody>
          <tr>
             <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-left:1px solid #333; border-right:1px solid #333;" align="center">
                 {{ $key+1 }}
             </td>
             <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                {{ (!empty($orderDetail['full_name']))?$orderDetail['full_name']:'Empty' }}
             </td>
             <!--<td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">-->
             <!--   {{ (!empty($orderDetail['number']))?$orderDetail['number']:'Empty' }}-->
             <!--</td>-->
             <!--<td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">-->
             <!--   {{ (!empty($orderDetail['email']))?$orderDetail['email']:'Empty' }}-->
             <!--</td>-->
             <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                {{ (!empty($orderDetail['facebook_name']))?$orderDetail['facebook_name']:'Empty' }}
             </td>
             <td style="font-family:Verdana, Geneva, sans-serif; font-weight:300; font-size:13px; border-bottom:1px solid #333; border-right:1px solid #333;" align="center">
                {{ (!empty($orderDetail['game_id']))?$orderDetail['game_id']:'Empty' }}
             </td>
          </tr></tbody>
          @endforeach
          @else
            Hello Admin, No users gets bonous today.
          @endif
  </table>
  <br>
  <br>
       
        
        Sincerely,
        <?php echo ($details['theme'] == 'default')?'Noor':ucfirst($details['theme']) ?> Games.
        @endcomponent
