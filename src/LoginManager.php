<?php

namespace dees040\AuthExtra;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\AuthManager as BaseAuthManager;

class LoginManager
{
    /**
     * The ConnectionInterface instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    private $connection;

    /**
     * The Locator instance.
     *
     * @var \dees040\AuthExtra\Locator
     */
    private $locator;

    /**
     * The AuthManager instance.
     *
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * LoginManager constructor.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \dees040\AuthExtra\Locator  $locator
     * @param  \Illuminate\Auth\AuthManager  $auth
     */
    public function __construct(ConnectionInterface $connection, Locator $locator, BaseAuthManager $auth)
    {
        $this->auth = $auth;
        $this->locator = $locator;
        $this->connection = $connection;
    }

    /**
     * Log a login attempt.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @param  bool  $success
     * @param  array|null  $credentials
     * @return void
     */
    public function log($user, $success, $credentials = null)
    {
        $this->getTable()->insert([
            'user_id' => $user ? $user->id : null,
            'ip' => $this->locator->getIp(),
            'country' => $this->locator->getCountry(),
            'success' => $success,
            'credentials' => $credentials,
        ]);
    }

    public function isSuspiciousLogin()
    {
        return false;
    }

    /**
     * Login the given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function login(Authenticatable $user)
    {
        $this->auth->login($user);
    }

    /**
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @return \dees040\AuthExtra\LoginManager
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Get the ConnectionInterface.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the table to interact with.
     *
     * @param  string  $table
     * @return \Illuminate\Database\Query\Builder
     */
    public function getTable($table = 'login_attempts')
    {
        return $this->connection->table($table);
    }
}