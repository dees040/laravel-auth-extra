<?php

namespace dees040\AuthExtra\Activations;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Foundation\Application;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Auth\Authenticatable;

class ActivationToken
{
    /**
     * The Connection interface.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    private $connection;

    /**
     * The key to use for hashing.
     *
     * @var string
     */
    private $hashKey;

    /**
     * The activations table.
     *
     * @var string
     */
    private $table;

    /**
     * The amount of time the token is valid.
     *
     * @var integer
     */
    private $expires;

    /**
     * ActivationToken constructor.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Foundation\Application  $application
     */
    public function __construct(ConnectionInterface $connection, Application $application)
    {
        $key = $application['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $this->expires = 3600;
        $this->hashKey = $key;
        $this->table = 'activations';
        $this->connection = $connection;
    }

    /**
     * Create a new token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return string
     */
    public function create(Authenticatable $user)
    {
        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the activation. Then we will insert a record in
        // the database so that we can verify the token within the activation.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($user->id, $token));

        return $token;
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(Authenticatable $user, $token)
    {
        $record = (array) $this->getTable()->where('user_id', $user->id)->first();

        return $record &&
            ! $this->tokenExpired($record['created_at'])
            && $token === $record['token'];
    }

    /**
     * Delete a token record.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function delete(Authenticatable $user)
    {
        $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * Retrieve an user by token.
     *
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUser($token)
    {
        $record = $this->getTable()
            ->where('token', $token)
            ->first(['user_id']);

        if (! $record) {
            return null;
        }

        return $this->getTable('users')
            ->where('id', $record->user_id)
            ->first();
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return int
     */
    protected function deleteExisting(Authenticatable $user)
    {
        return $this->getTable()->where('user_id', $user->id)->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param  integer  $id
     * @param  string  $token
     * @return array
     */
    protected function getPayload($id, $token)
    {
        return ['user_id' => $id, 'token' => $token, 'created_at' => new Carbon()];
    }

    /**
     * Get the table to interact with.
     *
     * @param  string  $table
     * @return \Illuminate\Database\Query\Builder
     */
    public function getTable($table = null)
    {
        $table = $table ?: $this->table;

        return $this->connection->table($table);
    }
}
