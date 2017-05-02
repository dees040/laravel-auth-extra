<?php

namespace dees040\AuthExtra;

use Illuminate\Config\Repository;
use dees040\AuthExtra\Exceptions\ConfigNotFoundExceptions;

class ConfigManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    private $config;

    /**
     * ConfigManager constructor.
     *
     * @param  \Illuminate\Config\Repository  $config
     * @throws \dees040\AuthExtra\Exceptions\ConfigNotFoundExceptions
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Determine if the auth manager needs to track login
     * attempts.
     *
     * @return bool
     */
    public function trackLoginAttempts()
    {
        return $this->getBoolean('track_login_attempts');
    }

    /**
     * Determine if the auth manager need to verify the
     * email address of a freshly registered user.
     *
     * @return bool
     */
    public function verifyEmail()
    {
        return $this->getBoolean('verify_email');
    }

    /**
     * Determine if the auth manager need to verify a login
     * attempt when the login attempt is suspicious.
     *
     * @return bool
     */
    public function verifySuspiciousLogin()
    {
        return $this->getBoolean('verify_login_attempt_on_suspicious_login');
    }

    /**
     * Get the login attempts model.
     *
     * @return mixed
     */
    public function loginAttemptsModel()
    {
        return $this->getConfigByKey('login_attempts_model');
    }

    /**
     * Get a notification class name.
     *
     * @param  string  $key
     * @return string
     */
    public function getNotification($key)
    {
        return $this->getConfigByKey("notifications.{$key}");
    }

    /**
     * Get a route.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getRoute($key)
    {
        return $this->getConfigByKey("routes.{$key}");
    }

    /**
     * Get a config by the given key.
     *
     * @param  string  $key
     * @return mixed
     * @throws \dees040\AuthExtra\Exceptions\ConfigNotFoundExceptions
     */
    public function getConfigByKey($key)
    {
        if (! $this->config->has('auth_extra')) {
            throw new ConfigNotFoundExceptions("The config file 'auth_extra.php' coudln't be found. Did you published the configuration?");
        }

        $fullKey = "auth_extra.{$key}";

        if (! $this->config->has($fullKey)) {
            throw new ConfigNotFoundExceptions("The config '{$key}' is not found.");
        }

        return $this->config->get($fullKey);
    }

    /**
     * Get a config and convert it to a boolean.
     *
     * @param  string  $key
     * @return bool
     */
    protected function getBoolean($key)
    {
        return (bool) $this->getConfigByKey($key);
    }
}