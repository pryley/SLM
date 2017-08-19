<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
	/**
	 * @return User
	 */
	public function create( Request $request )
	{
		$user = app( User::class );
		$this->validate( $request, $user->rules );
		$request['api_token'] = str_random( 60 );
		return $user->create( $request->all() );
	}
}
