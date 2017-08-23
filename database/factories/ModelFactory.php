<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define( App\Domain::class, function( Faker\Generator $faker ) {
	return [
		'domain' => $faker->domainName,
	];
});

/**
 * License key is generated in \App\Observers\LicenseObserver::class
 */
$factory->define( App\License::class, function( Faker\Generator $faker ) {
	$max_domains_allowed = [1,5,10][mt_rand( 0, 2 )];
	$status = ['active','active','active','inactive','revoked'][mt_rand( 0, 4 )];
	$deleted_at = $status == 'revoked' ? Carbon\Carbon::now() : null;
	$company = [null,$faker->company][mt_rand( 0, 1 )];
	return [
		'status' => $status,
		'first_name' => $faker->firstName,
		'last_name' => $faker->lastName,
		'email' => $faker->email,
		'company_name' => $company,
		'max_domains_allowed' => $max_domains_allowed,
		'transaction_id' => str_random( 32 ),
		'deleted_at' => $deleted_at,
	];
});

/**
 * Password is hashed and uid generated in App\Observers\UserObserver::class
 */
$factory->define( App\User::class, function( Faker\Generator $faker ) {
	return [
		'username' => $faker->userName,
		'email' => $faker->email,
		'password' => 'password',
		'role' => \App\User::BASIC_ROLE,
		'is_active' => [1,1,0][mt_rand( 0, 2 )],
	];
});
