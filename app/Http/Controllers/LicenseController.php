<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidDomainException;
use App\Exceptions\InvalidLicenseException;
use App\License;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
	/**
	 * @return array
	 */
	public function store( Request $request )
	{
		$license = app( License::class );
		$this->validate( $request, $license->rules );
		$license->forceFill([
			'company_name' => $request->input( 'company_name' ),
			'email' => $request->input( 'email' ),
			'expires_at' => Carbon::now()->addYear(),
			'first_name' => $request->input( 'first_name' ),
			'last_name' => $request->input( 'last_name' ),
			'max_domains_allowed' => $request->input( 'max_domains_allowed', 1 ),
			'status' => 'active',
			'transaction_id' => $request->input( 'transaction_id' ),
		])->save();
		return [
			'license_key' => $license->license_key,
			'max_domains_allowed' => $license->max_domains_allowed,
		];
	}

	/**
	 * @return License
	 */
	public function deactivate( Request $request )
	{
		$license = $this->update( $request, [
			'status' => 'inactive',
		]);
		$license->fireEvent( 'deactivated' );
		return $license;
	}

	/**
	 * @param string $licenseKey
	 * @return void
	 */
	public function destroy( $licenseKey )
	{
		if( $license = app( License::class )->withTrashed()->where( 'license_key', $licenseKey )->first() ) {
			$license->forceDelete();
			$license->fireEvent( 'removed' );
		}
	}

	/**
	 * @return License
	 */
	public function renew( Request $request )
	{
		$license = $this->update( $request, [
			'status' => 'active',
			'renewed_at' => Carbon::now(),
			'expires_at' => Carbon::now()->addYear(),
			'max_domains_allowed' => $request->input( 'max_domains_allowed', null ),
		]);
		$license->fireEvent( 'renewed' );
		return $license;
	}

	/**
	 * @return License
	 */
	public function restore( Request $request )
	{
		$license = $this->update( $request, [
			'status' => 'active',
		], true );
		$license->restore();
		return $license;
	}

	/**
	 * @return License
	 */
	public function revoke( Request $request )
	{
		$license = $this->update( $request, [
			'status' => 'revoked',
		]);
		$license->delete();
		$license->fireEvent( 'revoked' );
		return $license;
	}

	/**
	 * @return License
	 */
	public function verify( Request $request )
	{
		// \Log::debug( $request->ip() );
		// \Log::debug( $request->getHost() );
		$license = $this->getLicense( $request->input( 'license_key' ));
		if( $license->hasExpired() ) {
			$license->status = 'inactive';
		}
		if( $license->status != 'active' ) {
			throw new InvalidLicenseException;
		}
		if( !$license->hasDomain( $request->getHost() )) {
			throw new InvalidDomainException;
		}
		return $license;
	}

	/**
	 * @return License
	 */
	protected function update( Request $request, array $data, $isTrashed = false )
	{
		$license = $this->getLicense( $request->input( 'license_key' ), $isTrashed );
		foreach( $data as $key => $value ) {
			$license->$key = !is_null( $value ) ? $value : $license->$key;
		}
		$license->save();
		return $license;
	}
}
