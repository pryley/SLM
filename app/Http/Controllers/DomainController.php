<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Exceptions\InvalidDomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DomainController extends Controller
{
	/**
	 * @return void
	 */
	public function destroy( Request $request, $domain )
	{
		$license = $this->getLicense( $request->input( 'license_key' ));
		if( $domain = $license->hasDomain( $domain )) {
			$domain->forceDelete();
		}
	}

	/**
	 * @return Collection
	 */
	public function index( Request $request )
	{
		return $this->getLicense( $request->input( 'license_key' ))->domains->pluck( 'domain' );
	}

	/**
	 * @return Domain
	 */
	public function store( Request $request )
	{
		$domain = app( Domain::class );
		$license = $this->getLicense( $request->input( 'license_key' ));
		$this->validate( $request, $domain->rules );
		if( $license->hasDomain( $request->input( 'domain' ))) {
			throw new InvalidDomainException;
		}
		if( $license->domains()->count() >= $license->max_domains_allowed ) {
			throw new InvalidDomainException;
		}
		return $domain->create([
			'domain' => $request->input( 'domain' ),
			'license_id' => $license->id,
		]);
	}
}
