
  <?php
  if(isset($details1) && !empty($details1)){
      
         $name = isset($details1['name'])?$details1['name']:'';
         $form_id = isset($details1['form_id'])?$details1['form_id']:'';
         $message = $details1['message'];
         $days = $details1['days'];
         $token = URL::to('/remove-from-game/'. encrypt($form_id));
    }
  ?>
  <table style="width: 100%;border: 3px solid #dddddd;border-radius: 10px;padding: 20px 0 20px 0;width: 100%;">
   <tbody>
       <tr style="text-align: center;">
           <td>
           <?php 
                $active_theme = \App\Models\Theme::where('name',$details1['theme'])->first();
           ?>
           <img style="max-width: 20%;" src="<?php echo URL::to('/images/'.$details1['theme'].'/'.$active_theme->logo) ?>" alt="Game"> 
           </td>
       </tr>
       <tr>
           <td style="color: black;font-size: 15px;padding: 0 50px 0 50px;text-align:center;">
               Hello, <?= $name ?> ,<br>
               <?php echo  $message ?><br><br>
                

                If you wish to be removed from the game, 
                <br><br>
                <a target="_blank" href="<?= $token?>" style="color: #fbfcff;text-decoration: none;background: #59ed29;padding: 5px 5px 5px 5px;border-radius: 5px;text-align: center;">
                    Click Here
                </a>
                
               <br><br>
               Sincerely,<br>
               <b><?php echo ($details1['theme'] == 'default')?'Noor':ucfirst($details1['theme']) ?> Games </b>
           </td>
       </tr>
   </tbody>
</table>

