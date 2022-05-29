@component('mail::message')
    <table style="width: 100%;border: 3px solid #dddddd;border-radius: 10px;padding: 20px 0 20px 0;width: 100%;">
        <tbody>
            <tr style="text-align: center;">
                <?php 
                    if($details1['theme'] == 'default'){
                        $image = 'dragonnn.gif';
                    }else{
                        $image = 'logo.jpg';
                    }
                ?>
                <td>
                    <img style="max-width: 20%;" src="<?php echo URL::to('/images/'.$details1['theme'].'/'.$image) ?>" alt="Game">
                </td>
            </tr>
            <tr>
            <td style="color: black;font-size: 15px;padding: 0 50px 0 50px;text-align:center;">
                    <?php echo  $details1['text'] ?>
                    <br>
                    Sincerely,<br>
                    <b><?php echo ($details1['theme'] == 'Default')?'Noor':ucfirst($details1['theme']) ?> Games.</b>
                </td>
            </tr>
        </tbody>
    </table>
@endcomponent