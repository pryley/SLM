<?php

namespace Tests\Feature;

use App\Domain;
use App\License;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManageLicenseDomainsTest extends TestCase
{
	use DatabaseTransactions;

	protected function getLicenseKey( $domain = false )
	{
		$license = factory( License::class )->create();
		if( $domain ) {
			$license->domains()->save( factory( Domain::class )->make( ['domain' => $domain] ));
		}
		return $license->license_key;
	}

	private function validParams( $overrides = [] )
	{
		return array_merge([
			'domain' => 'geminilabs.io',
			'license_key' => '',
		], $overrides );
	}

	/** @test */
	public function add_a_domain_to_a_license()
	{
		$this->auth()->post( '/v1/domains', $this->validParams([
			'domain' => 'www.test.com',
			'license_key' => $this->getLicenseKey(),
		]))->seeJson([
			'status' => 200,
		]);

		// get License domains
		// $domain = $this->getDomainFromLicense( $this->response->original );

		// verify domain does not exist in License
		// verify License has not reached max_domains_allowed
		// add domain to License
	}

	/** @test */
	public function domain_field_is_required()
	{
		$this->auth()->post( '/v1/domains', [
			'license_key' => $this->getLicenseKey(),
		])->seeJson([
			'status' => 422,
			'errors' => (object) [
				'domain' => ['The domain field is required.']

			],
		]);
	}

	/** @test */
	public function domain_field_must_be_unique_for_license()
	{
		$this->auth()->post( '/v1/domains', $this->validParams([
			'domain' => 'www.test.com',
			'license_key' => $this->getLicenseKey( 'www.test.com' ),
		]))->seeJson([
			'status' => 403,
			'message' => 'Domain already exists',
		]);
	}

	/** @test */
	public function domain_field_must_exist_to_be_removed_from_a_license()
	{
		$this->auth()->delete( '/v1/domains/delete/www.test.com', [
			'license_key' => $this->getLicenseKey( 'www.test.com' ),
		])->seeJson([
			'status' => 404,
			'message' => 'The requested resource was not found',
		]);
	}

	public function get_all_domains_of_a_license()
	{
		// get License from license_key
		// return License->domains
	}

	public function license_key_field_is_required()
	{
	}

	public function license_key_field_must_be_valid()
	{
	}

	public function remove_a_domain_from_a_license()
	{
		// get License from license_key
		// verify domain exists in License
		// delete domain from License
		// return true
	}


	/** @test */
	public function license_allows_local_domains()
	{
		$license = factory( License::class )->create();
		$license->domains()->save( factory( Domain::class )->make( ['domain' => 'test.com'] ));

		$this->AssertTrue( $license->hasDomain( 'localhost' ));
		$this->AssertTrue( $license->hasDomain( '127.0.0.1' ));
		$this->AssertTrue( $license->hasDomain( '192.168.1.13' ));
		$this->AssertTrue( $license->hasDomain( '10.0.1.13' ));
		$this->AssertTrue( $license->hasDomain( 'test.dev' ));
		$this->AssertTrue( $license->hasDomain( 'test.local' ));
		$this->AssertTrue( $license->hasDomain( 'test.localhost' ));
		$this->AssertTrue( $license->hasDomain( 'test.test' ));
		$this->AssertTrue( $license->hasDomain( 'staging.test.test' ));
		$this->AssertTrue( $license->hasDomain( 'staging.test.com' ));
		$this->AssertTrue( $license->hasDomain( 'dev.test.com' ));

		$this->AssertFalse( $license->hasDomain( 'hello.test.com' ));
		$this->AssertFalse( $license->hasDomain( 'staging.test.net' ));
		$this->AssertFalse( $license->hasDomain( 'staging.test' ));
		$this->AssertFalse( $license->hasDomain( 'test' ));
	}
}
