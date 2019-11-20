<?php

namespace App\Observers;

use App\User;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class UserObserver
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return void
     */
    public function created(User $user)
    {
    }

    /**
     * @return void
     */
    public function creating(User $user)
    {
        if (is_string($user->password)) {
            $user->password = app('hash')->make($user->password);
        }
        $user->uuid = Uuid::uuid4()->toString();
    }

    /**
     * @return void
     */
    public function deleted(User $user)
    {
    }

    /**
     * @return void
     */
    public function updating(User $user)
    {
        if ($this->request->input('password')) {
            $user->password = app('hash')->make($this->request->input('password'));
        }
    }
}
