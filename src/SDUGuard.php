<?php

namespace SDU\MFA;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use SDU\MFA\Exceptions\MFAException;

class SDUGuard implements Guard
{
    private Request $request;
    private Dispatcher $events;

    private ?Authenticatable $user;

    public function __construct(Request $request)
    {
        $this->events  = app(Dispatcher::class);
        $this->request = $request;
        $this->user    = null;
    }

    public function check()
    {
        return (bool)$this->user();
    }

    public function guest()
    {
        return ! $this->check();
    }

    public function user()
    {
        if ($this->user == null && session()->has('sdu.mfa.user'))
            return session()->get('sdu.mfa.user');
        return $this->user;
    }

    public function id()
    {
        $user = $this->user();
        return $user->id ?? null;
    }

    /**
     * @throws MFAException
     */
    public function validate(array $credentials = [])
    {
        throw new MFAException("This method is not supported.");
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function login(Authenticatable $user)
    {
        session()->put('sdu.mfa.user', $user);
        $this->user = $user;
        $this->fireLoginEvent($user);
    }

    public function logout()
    {
        session()->forget('sdu.mfa.user');
        $this->user = null;
    }

    /**
     * Fire the login event if the dispatcher is set.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param bool $remember
     * @return void
     */
    protected function fireLoginEvent($user)
    {
        if (isset($this->events))
        {
            $this->events->dispatch(new Login(SDUGuard::class, $user, false));
        }
    }
}
