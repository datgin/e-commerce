<?php

namespace App\Listeners;

use App\Mail\Verify\SendMail;
use App\Events\RegisterSucess;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailVerify implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisterSucess $event): void
    {
        Mail::to($event->user->email)->send(new SendMail($event->user));
    }
}
