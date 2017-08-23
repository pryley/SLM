<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable, HasApiTokens, SoftDeletes;

	const ADMIN_ROLE = 'ADMIN_USER';
	const BASIC_ROLE = 'BASIC_USER';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'uid',
		'username',
		'email',
		'password',
		'role',
		'is_active',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
	];

	/**
	 * @var array
	 */
	public $rules = [
		'email' => 'required|email|unique:users',
		'username' => 'required|max:50',
		'password' => 'min:8',
	];
}
