<?php

namespace dees040\AuthExtra\Auth;

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
     * The AuthManager instance.
     *
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * The AuthLogger instance.
     *
     * @var \dees040\AuthExtra\Auth\AuthLogger
     */
    private $logger;

    /**
     * LoginManager constructor.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Auth\AuthManager  $auth
     * @param  \dees040\AuthExtra\Auth\AuthLogger  $logger
     */
    public function __construct(ConnectionInterface $connection, BaseAuthManager $auth, AuthLogger $logger)
    {
        $this->auth = $auth;
        $this->logger = $logger;
        $this->connection = $connection;
    }

    /**
     * Log a login attempt.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $success
     * @return void
     */
    public function log($user, $success)
    {
        $login = new SuspiciousLogin($user, $this->getConnection());

        if ($login->needsVerification()) {
            $this->logger->lock($user, $success, $login);
        } else {
            $this->logger->log($user, $success, $login);
        }
    }

    /**
     * Log the first login attempt which is created by
     * a register event.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function logFirst($user)
    {
        $login = new SuspiciousLogin(null, $this->getConnection());

        $this->logger->log($user, true, $login);
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
     * Logout the given user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function logout(Authenticatable $user)
    {
        $this->auth->logout($user);
    }

    /**
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @return \dees040\AuthExtra\Auth\LoginManager
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