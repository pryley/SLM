<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\Uri\Components\Host;

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
		'product_id' => 'required|exists:software,product_id',
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
		return $this->isLocalDomain( $domain ) || $this->domains()->where( 'domain', $domain )->first();
	}

	/**
	 * Determine if the license has a software set.
	 *
	 * @return bool
	 */
	public function hasSoftware( $productId )
	{
		return $this->software()->where( 'product_id', $productId )->first();
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

	/**
	 * @param string $domain
	 * @return bool
	 */
	protected function isLocalDomain( $domain )
	{
		if( $domain == 'localhost' ) {
			return true;
		}
		$host = new Host( $domain );
		if( $host->isIp() ) {
			return !filter_var( $host->createFromIp( $domain ), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
		}
		$domains = $this->domains->pluck( 'domain' )->toArray();
		if( empty( $domains )) {
			return false;
		}
		foreach( $domains as $existingDomain ) {
			if( !$this->isLocalValidDomain( $existingDomain, $host )) {
				return false;
			}
		}
		return true;
	}

	/**
	 * @param string $domain
	 * @return bool
	 */
	protected function isLocalValidDomain( $domain, Host $host )
	{
		$registeredHost = new Host( $domain );
		if( $this->removeSuffixFromDomain( $host ) != $this->removeSuffixFromDomain( $registeredHost )) {
			return false;
		}
		$subdomainWhitelist = array_unique( ['dev', 'staging', $registeredHost->getSubdomain()] );
		$suffixWhitelist = array_unique( ['dev', $registeredHost->getPublicSuffix()] );
		if(( !in_array( $host->getPublicSuffix(), $suffixWhitelist ) && $host->isPublicSuffixValid() )
			|| !in_array( $host->getSubdomain(), $subdomainWhitelist )) {
			return false;
		}
		return true;
	}

	/**
	 * @return string
	 */
	protected function removeSuffixFromDomain( Host $host )
	{
		return substr( $host->getRegisterableDomain(), 0, -strlen( $host->getPublicSuffix() ));
	}
}
