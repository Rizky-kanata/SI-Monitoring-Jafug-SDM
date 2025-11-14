<?php

namespace App\Jobs;

use App\Services\BrevoMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPasswordOtpEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $email,
        public ?string $name,
        public string $code,
        public int $expiresInMinutes = 10
    ) {
    }

    public function handle(BrevoMailer $mailer): void
    {
        $html = view('emails.password_otp', [
            'code' => $this->code,
            'recipientName' => $this->name,
            'expiresInMinutes' => $this->expiresInMinutes,
            'appName' => config('app.name'),
        ])->render();

        $mailer->send(
            $this->email,
            $this->name,
            'Kode OTP Reset Password Portal Admin RIIB',
            $html
        );
    }
}
