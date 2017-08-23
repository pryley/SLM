<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get( '/', function() use( $app ) {
	return $app->version();
});



$app->group( ['prefix' => 'v1'], function() use( $app ) {
	// public routes
	$app->group( ['middleware' => 'throttle:20,1'], function() use( $app ) {
		$app->post( 'licenses/verify', 'LicenseController@verify' );
	});
	// oauth2 routes
	\Dusterio\LumenPassport\LumenPassport::routes( $app, [
		'prefix' => 'oauth',
		'middleware' => ['throttle:60,1'],
	]);
	// protected routes
	$app->group( ['middleware' => ['auth:api', 'throttle:60,1']], function() use( $app ) {
		// licenses
		$app->post( 'licenses', 'LicenseController@store' );
		$app->post( 'licenses/deactivate', 'LicenseController@deactivate' );
		$app->delete( 'licenses/{license_key}', 'LicenseController@destroy' );
		$app->post( 'licenses/renew', 'LicenseController@renew' );
		$app->post( 'licenses/restore', 'LicenseController@restore' );
		$app->post( 'licenses/revoke', 'LicenseController@revoke' );
		// domains
		$app->delete( 'domains/{domain}', 'DomainController@destroy' );
		$app->get( 'domains', 'DomainController@index' );
		$app->post( 'domains', 'DomainController@store' );
	});
});
