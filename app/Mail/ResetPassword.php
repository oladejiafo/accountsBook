<?php

namespace App\Mail;

// use Queueable, SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{

        public $token;
    
        /**
         * Create a new message instance.
         *
         * @return void
         */
        public function __construct($token)
        {
            $this->token = $token;
        }
    
        /**
         * Build the message.
         *
         * @return $this
         */
        public function build()
        {
            return $this->subject('Password Reset')->view('emails.resetpassword-template');
        }
    }
    