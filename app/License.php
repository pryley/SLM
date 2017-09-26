<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class License extends Model
{
	use SoftDeletes;

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = [
		'deleted_at',
		'expires_at',
		'renewed_at',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'company_name',
		'email',
		'first_name',
		'last_name',
		'max_domains_allowed',
		'status',
		'transaction_id',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'id',
		'license_key',
		'updated_at',
	];

	/**
	 * These are extra user-defined events observers may subscribe to.
	 *
	 * @var array
	 */
	protected $observables = [
		'deactivated',
		'removed',
		'renewed',
		'revoked',
	];

	/**
	 * @var array
	 */
	public $rules = [
		'email' => 'required|email',
		'first_name' => 'required',
		'last_name' => 'required',
		'software' => 'required|exists:software,slug',
		'transaction_id' => 'required|unique:licenses',
	];

	/**
	 * Get all of the domains for the license.
	 */
	public function domains()
	{
		return $this->hasMany( Domain::class, 'license_id' );
	}

	/**
	 * Determine if the license has a domain set.
	 *
	 * @return bool
	 */
	public function hasDomain( $domain )
	{
		return $this->domains()->where( 'domain', $domain )->first();
	}

	/**
	 * Determine if the license has a software set.
	 *
	 * @return bool
	 */
	public function hasSoftware( $software )
	{
		return $this->software()->where( 'slug', $software )->first();
	}

	/**
	 * Determine if the license is expired.
	 *
	 * @return bool
	 */
	public function hasExpired()
	{
		return Carbon::now()->subWeek()->gte( Carbon::parse( $this->expires_at ));
	}

	/**
	 * Get the software for the license.
	 */
	public function software()
	{
		return $this->belongsToMany( Software::class, 'software_licenses', 'license_id', 'software_id' );
	}
}
