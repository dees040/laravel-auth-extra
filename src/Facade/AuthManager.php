<?php

namespace dees040\AuthExtra\Facade;

use Illuminate\Support\Facades\Facade;

class AuthManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'dees040\AuthExtra\AuthManager';
    }
}