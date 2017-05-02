<?php

namespace dees040\AuthExtra;

use Illuminate\Http\Request;
use dees040\AuthExtra\Auth\LoginManager;
use Illuminate\Contracts\Auth\Authenticatable;
use dees040\AuthExtra\Activations\ActivationManager;

class AuthManager
{
    /**
     * The ConfigManager instance.
     *
     * @var \dees040\AuthExtra\ConfigManager
     */
    private $config;

    /**
     * The LoginManager instance.
     *
     * @var \dees040\AuthExtra\Auth\LoginManager
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
     * @param  \dees040\AuthExtra\ConfigManager  $config
     * @param  \dees040\AuthExtra\Auth\LoginManager  $loginManager
     * @param  \dees040\AuthExtra\Activations\ActivationManager  $activations
     */
    public function __construct(ConfigManager $config, LoginManager $loginManager, ActivationManager $activations)
    {
        $this->config = $config;
        $this->activations = $activations;
        $this->loginManager = $loginManager;
    }

    /**
     * Verify the activation token given in the route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyActivationToken(Request $request)
    {
        $user = $this->activations->verify($request->get('token'));

        if (is_null($user)) {
            abort(403);
        }

        $this->loginManager->login($user);

        return redirect('/');
    }

    /**
     * Verify the login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyLogin(Request $request)
    {
        return redirect('/');
    }

    /**
     * Determine if the given user is blocked.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    public function userIsBlocked(Authenticatable $user)
    {
        $attempt = $this->getLoginManager()->getTable()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return $attempt->type == 9;
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
     * @return \dees040\AuthExtra\Auth\LoginManager
     */
    public function getLoginManager()
    {
        return $this->loginManager;
    }
}