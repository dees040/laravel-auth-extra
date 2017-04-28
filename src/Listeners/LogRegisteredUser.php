<?php

namespace dees040\AuthExtra\Listeners;

use Illuminate\Auth\Events\Registered;
use dees040\AuthExtra\Listeners\AuthExtraListener as Listener;

class LogRegisteredUser extends Listener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if ($this->config()->verifyEmail()) {
            $this->manager->sendVerificationEmail($event->user);
        }
    }
}