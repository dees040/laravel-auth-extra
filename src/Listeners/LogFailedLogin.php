<?php

namespace dees040\AuthExtra\Listeners;

use Illuminate\Auth\Events\Failed;
use dees040\AuthExtra\Listeners\AuthExtraListener as Listener;

class LogFailedLogin extends Listener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        if ($this->config()->trackLoginAttempts()) {
            $this->manager->getLoginManager()->log($event->user, false);
        }
    }
}
