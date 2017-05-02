<?php

namespace dees040\AuthExtra\Activations;

use dees040\AuthExtra\ConfigManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivationManager
{
    use HandlesAuthorization;

    /**
     * The ActivationToken instance.
     *
     * @var \dees040\AuthExtra\Activations\ActivationToken
     */
    private $token;

    /**
     * The ConfigManager instance.
     *
     * @var \dees040\AuthExtra\ConfigManager
     */
    private $config;

    /**
     * ActivationManager constructor.
     *
     * @param  \dees040\AuthExtra\Activations\ActivationToken  $token
     * @param  \dees040\AuthExtra\ConfigManager  $config
     */
    public function __construct(ActivationToken $token, ConfigManager $config)
    {
        $this->token = $token;
        $this->config = $config;
    }

    /**
     * Create a new token for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    public function create(Authenticatable $user)
    {
        $token = $this->token->create($user);

        $notification = $this->config->getNotification('verify_email');

        $user->notify(new $notification($token));
    }

    /**
     * Verify and delete the user if there is a correct token.
     *
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function verify($token)
    {
        $user = $this->token->getUser($token);

        if (! $user || ! $this->token->exists($user, $token)) {
            return null;
        }

        $this->token->delete($user);

        return $user;
    }

    /**
     * Determine if the user is verified.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    public function isVerified($user)
    {
        $record = $this->token->getTable()->where('email', $user->email)->first();

        return is_null($record);
    }

    /**
     * Determine if the user is not verified.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    public function isNotVerified($user)
    {
        return ! $this->isVerified($user);
    }
}
