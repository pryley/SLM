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
	public function index( Request $request )
	{
		return $this->respondWithCollection( app( User::class )->all(), $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store( Request $request )
	{
		$user = app( User::class );
		$this->validate( $request, $user->rules );
		return $this->respondWithItem( $user->create([
			'email' =>  $request->input( 'email' ),
			'username' =>  $request->input( 'username' ),
			'is_active' => $request->input( 'is_active', 1 ),
			'role' => $request->input( 'role', User::BASIC_ROLE )
		]), $this->transformer );
	}
}
