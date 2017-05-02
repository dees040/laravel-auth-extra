<?php

namespace dees040\AuthExtra\Auth;

use Carbon\Carbon;
use dees040\AuthExtra\Locator;

class SuspiciousLogin
{
    /**
     * The Authenticatable instance.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    private $user;

    /**
     * The Connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    private $connection;

    /**
     * The suspicious points.
     *
     * @var int
     */
    private $points = 0;

    /**
     * The calculators to use.
     *
     * @var array
     */
    private $calculators = [
        'ip',
        'country',
        'attempt',
        'time',
        'browser',
    ];

    /**
     * The Locator instance.
     *
     * @var \dees040\AuthExtra\Locator
     */
    private $locator;

    /**
     * SuspiciousLogin constructor.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     */
    public function __construct($user, $connection)
    {
        $this->user = $user;
        $this->connection = $connection;
        $this->locator = new Locator(request());

        $this->calculate();
    }

    /**
     * Calculate how many suspicious point the login gets.
     */
    private function calculate()
    {
        if (is_null($this->user)) {
            return;
        }

        foreach ($this->calculators as $calculator) {
            $method = 'calculate'.ucfirst($calculator).'Points';

            try {
                $this->points += $this->$method($this->user);
            } catch (\Exception $e) {
                dd($e->getMessage(), $e->getLine());
            }
        }
    }

    /**
     * Calculate the points based on IP.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return int
     */
    protected function calculateIpPoints($user)
    {
        $ip = $this->locator->getIp();
        $first = $this->getTable()->where('user_id', $user->id)
            ->oldest()
            ->first();

        if (is_null($first)) {
            return 0;
        }

        $mostUsed = $this->getTable()->where('user_id', $user->id)
            ->where('success', 1)
            ->select(['ip', \DB::raw('count(ip) as ip_occurrence')])
            ->groupBy('ip')
            ->orderByDesc('ip_occurrence')
            ->first();

        if ($first->ip == $ip || $mostUsed->ip == $ip) {
            return 0;
        }

        return 5;
    }

    /**
     * Calculate the points based on IP.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return int
     */
    protected function calculateCountryPoints($user)
    {
        $country = $this->locator->getCountry();
        $first = $this->getTable()->where('user_id', $user->id)->oldest()->first();

        if (is_null($first) || $country == $first->country) {
            return 0;
        }

        return 50;
    }

    /**
     * Calculate the points based on how many attempts there are.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return int
     */
    protected function calculateAttemptPoints($user)
    {
        $attempts = $this->getTable()->where('user_id', $user->id)->oldest()->limit(3)->get();

        foreach ($attempts as $attempt) {
            if ($attempt->success == 1) {
                return 0;
            }
        }

        return 45;
    }

    /**
     * Calculate the points based on how much time there is between
     * logins.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return int
     */
    protected function calculateTimePoints($user)
    {
        $attempts = $this->getTable()->where('user_id', $user->id)->oldest()->limit(2)->get();

        if (count($attempts) == 1) {
            $first = new Carbon($attempts[0]->created_at);
            $second = Carbon::now();
        } elseif (count($attempts) == 2) {
            $first = new Carbon($attempts[0]->created_at);
            $second = new Carbon($attempts[1]->created_at);
        } else {
            return 0;
        }

        $difference = $first->diffInSeconds($second);

        if ($difference > 1) {
            return 0;
        }

        return 50;
    }

    /**
     * Calculate the points based on the user it's browser.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return int
     */
    protected function calculateBrowserPoints($user)
    {
        // In the future we might want to check if the browser
        // has changed.

        return 0;
    }

    /**
     * Determine if the current login needs verification.
     *
     * @return bool
     */
    public function needsVerification()
    {
        return $this->points > 50;
    }

    /**
     * Get the suspicious login points.
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Return the Locator instance.
     *
     * @return \dees040\AuthExtra\Locator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Create a new Query Builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table('login_attempts');
    }
}
