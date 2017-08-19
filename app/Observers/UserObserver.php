<?php

namespace App\Observers;

use App\User;
use Hash;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class UserObserver
{
	/**
	 * @var Request
	 */
	protected $request;

	public function __construct( Request $request )
	{
		$this->request = $request;
	}

	/**
	 * @return void
	 */
	public function created( User $user )
	{
		\Log::debug( 'User created' );
	}

	/**
	 * @return void
	 */
	public function creating( User $user )
	{
		$user->uid = Uuid::uuid4()->toString();
		$user->password = Hash::make( $user->password );
	}

	/**
	 * @return void
	 */
	public function deleted( User $user )
	{
		\Log::debug( 'User deleted' );
	}

	/**
	 * @return void
	 */
	public function updating( User $user )
	{
		if( $this->request->input( 'password' )) {
			$user->password = Hash::make( $this->request->input( 'password' ));
		}
	}
}
