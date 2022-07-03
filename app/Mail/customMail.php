<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class customMail extends Mailable {
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
        $details = json_decode($this->details, true);
        $subject = 'Users Updated List';
        $settings = GeneralSetting::first();
        $site_email=$settings->site_email;
          $sitename=$settings->sitename;
        
        return $this->from($site_email, $sitename)
                    ->subject($subject)
                    ->markdown('mails.exmpl')
                    ->with([
                        'details' => (!empty($details) ? $details : '') 
                           ]);
    }
}
