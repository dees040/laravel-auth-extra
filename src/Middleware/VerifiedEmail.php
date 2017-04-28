<?php

namespace dees040\AuthExtra\Middleware;

use Closure;
use Illuminate\Auth\Access\HandlesAuthorization;
use dees040\AuthExtra\Activations\ActivationManager;

class VerifiedEmail
{
    use HandlesAuthorization;

    /**
     * The ActivationManager instance.
     *
     * @var \dees040\AuthExtra\Activations\ActivationManager
     */
    private $manager;

    /**
     * VerifiedEmail constructor.
     *
     * @param  \dees040\AuthExtra\Activations\ActivationManager  $manager
     */
    public function __construct(ActivationManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->manager->isNotVerified($request->user())) {
            $this->deny();
        }

        return $next($request);
    }
}
