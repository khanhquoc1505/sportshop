<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangeCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $code;

    public function __construct($userName, $code)
    {
        $this->userName = $userName;
        $this->code     = $code;
    }

    public function build()
    {
        return $this
            ->subject('Mã xác nhận của bạn')
            ->view('emails.password_change_code')
            ->with([
                'userName' => $this->userName,
                'code'     => $this->code,
            ]);
    }
}
