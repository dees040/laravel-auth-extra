<?php

namespace dees040\AuthExtra\Auth;

use Carbon\Carbon;
use dees040\AuthExtra\Locator;
use dees040\AuthExtra\ConfigManager;
use Illuminate\Database\ConnectionInterface;

class AuthLogger
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
     * The ConfigManager instance.
     *
     * @var \dees040\AuthExtra\ConfigManager
     */
    private $config;

    /**
     * AuthLogger constructor.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \dees040\AuthExtra\Locator  $locator
     * @param  \dees040\AuthExtra\ConfigManager  $config
     */
    public function __construct(ConnectionInterface $connection, Locator $locator, ConfigManager $config)
    {
        $this->config = $config;
        $this->locator = $locator;
        $this->connection = $connection;
    }

    /**
     * Lock the user. The user needs to verify the login via email.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $success
     * @param  \dees040\AuthExtra\Auth\SuspiciousLogin  $login
     */
    public function lock($user, $success, $login)
    {
        if ($this->config->verifySuspiciousLogin()) {
            $this->log($user, $success, $login, 9);

            $notification = $this->config->getNotification('verify_login');

            $user->notify(new $notification($login->getLocator()));
        } else {
            $this->log($user, $success, $login);
        }
    }

    /**
     * Log a login attempt.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  bool  $success
     * @param  \dees040\AuthExtra\Auth\SuspiciousLogin  $login
     * @param  null|int  $type
     */
    public function log($user, $success, $login, $type = null)
    {
        $this->getTable()->insert([
            'user_id' => $user ? $user->id : null,
            'ip' => $this->locator->getIp(),
            'country' => $this->locator->getCountry(),
            'city' => $this->locator->getCity(),
            'success' => $success,
            'type' => $this->getType($success, $type),
            'suspicious' => $login->getPoints(),
            'created_at' => new Carbon(),
            'updated_at' => new Carbon(),
        ]);
    }

    /**
     * Determine the login type.
     *
     * @param  bool  $success
     * @param  int  $type
     * @return int
     */
    protected function getType($success, $type)
    {
        if (is_null($type)) {
            return $success ? 0 : 1;
        }

        return $type;
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
