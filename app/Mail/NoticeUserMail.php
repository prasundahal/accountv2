<?php

namespace App\Mail;

use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoticeUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details) {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $settings = GeneralSetting::first();
        // $details = $this->details;
        $subject = 'New Registration Successfull';
        $details1 = [
            'text' => $this->details,
            'theme' => ($settings->theme)
        ];
        // $details= $subject;
       
      $site_email=$settings->site_email;
          $sitename=$settings->sitename;
        
        return $this->from($site_email, $sitename)
                    ->subject($subject)
                    ->markdown('mails.noticeuser')
                    ->with([
                        'details1' => (!empty($details1) ? $details1 : '') 
                           ]);
    }
}