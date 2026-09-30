<?php

namespace App\Jobs\Auth;

use App\Mail\Auth\EmailToDoFirstAccess;
use App\Mail\Auth\EmailToDoFirstAccessInvite;
use App\Mail\Auth\EmailToDoFirstAccessManual;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendEmailToDoFirstAccess implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $time,
        public readonly string $token,
        public readonly string $password
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new EmailToDoFirstAccessManual(
            $this->user->name,
            $this->user->email,
            $this->time,
            $this->token,
            $this->password
        );
        Mail::to($this->user)->send($email);
    }
}
