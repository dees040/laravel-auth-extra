<?php

namespace dees040\AuthExtra\Listeners;

use Illuminate\Auth\Events\Login;
use dees040\AuthExtra\Listeners\AuthExtraListener as Listener;

class LogSuccessfulLogin extends Listener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        if ($this->config()->trackLoginAttempts()) {
            $this->manager->getLoginManager()->log($event->user, true);
        }

        if ($event->user->isBlocked()) {
            abort(403, 'You need to verify your identity.');

            $this->manager->getLoginManager()->logout($event->user);
        }
    }
}