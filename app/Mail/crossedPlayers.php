<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class crossedPlayers extends Mailable {
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
        // dd($details['token']);
        dd('success');
        $subject = 'Congratulations You are Eligible for Spinner';
        return $this->from('noorgames@gmail.com', 'Noor Games')
                    ->subject($subject)
                    ->markdown('mails.exmpl2')
                    ->with([
                        'token' => (!empty($details) ? $details['token'] : '') 
                           ]);
    }
}
