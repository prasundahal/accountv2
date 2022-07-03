@component('mail::message')
  @if(isset($details) && !empty($details))
      @php
        $details = json_decode($details, true);
        $message = $details['message'];
         $extra_details = json_decode($details['details']);
      @endphp
  @endif
  <table style="width: 100%;border: 3px solid #dddddd;border-radius: 10px;padding: 20px 0 20px 0;width: 100%;">
   <tbody>
       <tr style="text-align: center;">
           <td>
           <?php 
                $active_theme = \App\Models\Theme::where('name',$details['theme'])->first();
           ?>
           <img style="max-width: 20%;" src="<?php echo URL::to('/images/'.$details['theme'].'/'.$active_theme->logo) ?>" alt="Game"> 
           </td>
       </tr>
       <tr>
           <td style="color: black;font-size: 15px;padding: 0 50px 0 50px;text-align:center;">
               <?php echo  $message ?><br><br>
               <?php 
                    foreach($extra_details as $a => $b){
                        echo $a.' is '.$b.'<br>';
                    }

                ?>
               <br><br>
               Sincerely,<br>
               <b><?php echo ($details['theme'] == 'default')?'Noor':ucfirst($details['theme']) ?> Games </b>
           </td>
       </tr>
   </tbody>
</table>
@endcomponent
