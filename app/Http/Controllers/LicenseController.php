<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidDomainException;
use App\Exceptions\InvalidLicenseException;
use App\Exceptions\InvalidSoftwareException;
use App\License;
use App\Software;
use App\Transformers\LicenseTransformer;
use App\Transformers\LicensePublicTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
	public function __construct( LicenseTransformer $transformer )
	{
		$this->transformer = $transformer;
		parent::__construct();
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function index()
	{
		return $this->respondWithCollection( app( License::class )->all(), $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
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
		$this->attachLicenseToSoftware( $request->input( 'software' ), $license->id );
		return $this->respondWithItem( $license, $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function deactivate( Request $request )
	{
		$license = $this->updateLicense( $request, [
			'status' => 'inactive',
		]);
		$license->fireEvent( 'deactivated' );
		return $this->respondWithItem( $license, $this->transformer );
	}

	/**
	 * @param string $licenseKey
	 * @return \Illuminate\Http\JsonResponse
	 * @throws InvalidLicenseException
	 */
	public function destroy( $licenseKey )
	{
		if( $license = app( License::class )->withTrashed()->where( 'license_key', $licenseKey )->first() ) {
			$license->forceDelete();
			$license->fireEvent( 'removed' );
			return $this->sendCustomResponse( 204, 'License deleted' );
		}
		throw new InvalidLicenseException;
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function renew( Request $request )
	{
		$license = $this->updateLicense( $request, [
			'status' => 'active',
			'renewed_at' => Carbon::now(),
			'expires_at' => Carbon::now()->addYear(),
			'max_domains_allowed' => $request->input( 'max_domains_allowed', null ),
		]);
		$license->fireEvent( 'renewed' );
		return $this->respondWithItem( $license, $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function restore( Request $request )
	{
		$license = $this->updateLicense( $request, [
			'status' => 'active',
		], true );
		$license->restore();
		return $this->respondWithItem( $license, $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function revoke( Request $request )
	{
		$license = $this->updateLicense( $request, [
			'status' => 'revoked',
		]);
		$license->delete();
		$license->fireEvent( 'revoked' );
		return $this->respondWithItem( $license, $this->transformer );
	}

	/**
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function verify( Request $request, LicensePublicTransformer $transformer )
	{
		$license = $this->getLicense( $request->input( 'license_key' ));
		if( !$this->verifyLicense( $license )) {
			throw new InvalidLicenseException( $license->status );
		}
		if( !$this->verifySoftware( $license, $request->input( 'software' ))) {
			throw new InvalidSoftwareException;
		}
		if( app()->environment( 'production' ) && !$license->hasDomain( $request->getHost() )) {
			throw new InvalidDomainException;
		}
		return $this->respondWithItem( $license, $transformer );
	}

	/**
	 * @return void
	 * @throws InvalidSoftwareException
	 */
	protected function attachLicenseToSoftware( $softwareSlug, $licenseId )
	{
		if( $software = app( Software::class )->where( 'slug', $softwareSlug )->first() ) {
			$software->licenses()->attach( $licenseId );
			return;
		}
		throw new InvalidSoftwareException;
	}

	/**
	 * @return License
	 */
	protected function updateLicense( Request $request, array $data, $isTrashed = false )
	{
		$license = $this->getLicense( $request->input( 'license_key' ), $isTrashed );
		if( array_key_exists( 'renewed_at', $data ) && $data['renewed_at'] instanceof Carbon ) {
			$license->num_times_renewed += 1;
		}
		foreach( $data as $key => $value ) {
			$license->$key = !is_null( $value ) ? $value : $license->$key;
		}
		$license->save();
		return $license;
	}

	/**
	 * @return bool
	 */
	protected function verifyLicense( License $license )
	{
		if( $license->hasExpired() ) {
			$license->status = 'expired';
			$license->save();
		}
		return $license->status == 'active';
	}

	/**
	 * @return bool
	 */
	protected function verifySoftware( License $license, $softwareSlug )
	{
		return $license->hasSoftware( $softwareSlug );
	}
}
