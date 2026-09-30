<?php

namespace App\Jobs\Auth;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Mail\Auth\EmailToDoFirstAccessInvite;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendEmailToDoFirstAccessOther implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $time,
        public readonly string $token
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = new EmailToDoFirstAccessInvite(
            $this->user->name,
            $this->user->email,
            $this->time,
            $this->token
        );

        Mail::to($this->user)->send($email);
    }
}
