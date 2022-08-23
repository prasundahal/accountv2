<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Log;

class UnsubscribeMail extends Mailable
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
        // Log::channel('cronLog')->info($this->details);
        $details = json_decode($this->details, true);
        $subject = $details['subject'];

        $settings = GeneralSetting::first();
        $title = ($settings->theme == 'default')?'Noor':ucwords($settings->theme);

        return $this->from('noorgames@gmail.com', $title.' Games')
                    ->subject($subject)
                    ->markdown('mails.unsub')
                    ->with([
                        'details' => (!empty($details) ? $details : '') 
                           ]);
    }
}
