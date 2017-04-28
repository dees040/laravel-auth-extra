<?php

namespace dees040\AuthExtra\Listeners;

use Illuminate\Auth\Events\Attempting;
use dees040\AuthExtra\Listeners\AuthExtraListener as Listener;

class LogAuthenticationAttempt extends Listener
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Attempting  $event
     * @return void
     */
    public function handle(Attempting $event)
    {
        if ($this->config()->verifySuspiciousLogin()) {
            $this->manager->checkForSuspiciousLogin();
        }
    }
}