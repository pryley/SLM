<?php

namespace App\Http\Controllers;

use App\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;

class UserController extends Controller
{
	public function __construct( UserTransformer $transformer )
	{
		$this->transformer = $transformer;
		parent::__construct();
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function create( Request $request )
	{
		$user = app( User::class );
		$this->validate( $request, $user->rules );
		$request['api_token'] = str_random( 60 );
		return $this->respondWithItem( $user->create( $request->all() ), $this->transformer );
	}
}
