<?php

namespace dees040\AuthExtra;

use dees040\AuthExtra\Facade\AuthManager;

trait ExtraAuthenticatable
{
    /**
     * Get the login attempts from the model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function loginAttempts()
    {
        $loginAttemptModel = AuthManager::getConfig()->loginAttemptsModel();

        if ($loginAttemptModel) {
            return $this->hasMany($loginAttemptModel);
        }

        return AuthManager::getLoginAttempts($this);
    }

    /**
     * Determine if the current model has a verified email
     * address.
     *
     * @return bool
     */
    public function verifiedEmail()
    {
        return AuthManager::getActivationManager()->isVerified($this);
    }

    /**
     * Determine if the current model has been blocked.
     * This means there has been a suspicious login and the
     * user needs to verify the login attempt.
     *
     * @return bool
     */
    public function isBlocked()
    {
        return AuthManager::userIsBlocked($this);
    }
}
