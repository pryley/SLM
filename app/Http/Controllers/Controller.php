<?php

namespace App\Http\Controllers;

use App\License;
use App\Exceptions\InvalidLicenseException;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
	/**
	 * @param string $licenseKey
	 * @param bool $isTrashed
	 * @return License
	 * @throws InvalidLicenseException
	 */
	public function getLicense( $licenseKey, $isTrashed = false )
	{
		$model = $isTrashed
			? app( License::class )->onlyTrashed()
			: app( License::class );
		if( $license = $model->where( 'license_key', $licenseKey )->first() ) {
			return $license;
		}
		throw new InvalidLicenseException;
	}
}
