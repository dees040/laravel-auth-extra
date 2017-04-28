<?php

namespace dees040\AuthExtra;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Auth\Authenticatable;
use dees040\AuthExtra\Activations\ActivationManager;

class AuthManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    private $app;

    /**
     * The ConfigManager instance.
     *
     * @var \dees040\AuthExtra\ConfigManager
     */
    private $config;

    /**
     * The LoginManager instance.
     *
     * @var \dees040\AuthExtra\LoginManager
     */
    private $loginManager;

    /**
     * The ActivationManager instance.
     *
     * @var \dees040\AuthExtra\Activations\ActivationManager
     */
    private $activations;

    /**
     * AuthManager constructor.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \dees040\AuthExtra\ConfigManager  $config
     * @param  \dees040\AuthExtra\LoginManager  $loginManager
     * @param  \dees040\AuthExtra\Activations\ActivationManager  $activations
     */
    public function __construct(
        Application $app,
        ConfigManager $config,
        LoginManager $loginManager,
        ActivationManager $activations)
    {
        $this->app = $app;
        $this->config = $config;
        $this->activations = $activations;
        $this->loginManager = $loginManager;
    }

    /**
     * Send a email to verify the user it's email address.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function sendVerificationEmail(Authenticatable $user)
    {
        $this->activations->create($user);
    }

    /**
     * Verify the activation token given in the route.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function verifyActivationToken($token)
    {
        $user = $this->activations->verify($token);

        if (is_null($user)) {
            abort(403);
        }

        $this->loginManager->login($user);

        return redirect('/');
    }

    /**
     * Determine if a login is suspicious and take action
     * if it looks suspicious.
     *
     * @return void
     */
    public function checkForSuspiciousLogin()
    {
        if ($this->loginManager->isSuspiciousLogin()) {
            $this->loginManager->verifyUser();
        }
    }

    /**
     * Get the login attempts from the given users..
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Support\Collection
     */
    public function getLoginAttempts(Authenticatable $user)
    {
        return $this->loginManager->getTable()
            ->where('user_id', $user->id)
            ->get();
    }

    /**
     * Get the config manager instance.
     *
     * @return \dees040\AuthExtra\ConfigManager
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Return the ActivationManager instance.
     *
     * @return \dees040\AuthExtra\Activations\ActivationManager
     */
    public function getActivationManager()
    {
        return $this->activations;
    }

    /**
     * Return the LoginManager instance.
     *
     * @return \dees040\AuthExtra\LoginManager
     */
    public function getLoginManager()
    {
        return $this->loginManager;
    }
}