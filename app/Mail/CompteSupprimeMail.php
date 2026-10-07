<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompteSupprimeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $prenom,
        public string $email,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Confirmation de suppression de votre compte Sentinel IA')
            ->view('emails.compte-supprime');
    }
}
