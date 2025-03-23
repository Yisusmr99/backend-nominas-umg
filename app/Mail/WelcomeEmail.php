<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;

    public function __construct($userData)
    {
        $this->userData = $userData;
    }

    public function build()
    {
        return $this->markdown('emails.welcome')
            ->subject('Bienvenido a nuestro sistema');
    }
}
