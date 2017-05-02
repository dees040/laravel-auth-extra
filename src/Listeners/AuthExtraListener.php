<?php

namespace dees040\AuthExtra\Listeners;

use dees040\AuthExtra\AuthManager;

abstract class AuthExtraListener
{
    /**
     * The AuthManager instance.
     *
     * @var \dees040\AuthExtra\AuthManager
     */
    protected $manager;

    /**
     * AuthExtraListener constructor.
     *
     * @param  \dees040\AuthExtra\AuthManager  $manager
     */
    public function __construct(AuthManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get the config from the AuthManager instance.
     *
     * @return \dees040\AuthExtra\ConfigManager
     */
    protected function config()
    {
        return $this->manager->getConfig();
    }
}
