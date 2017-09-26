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
		// $this->actingAs( factory( User::class )->create(), 'api' );

		$license = factory( License::class )->create();
		$this->auth()->post( '/v1/domains', $this->validParams([
			'domain' => 'www.test.com',
			'license_key' => $license->license_key,
		]));

		// error_log( print_r( $this->response, 1 ));
		$this->assertResponseStatus( 200 );

		// get License domains
		// $domain = $this->getDomainFromLicense( $this->response->original );

		// verify domain does not exist in License
		// verify License has not reached max_domains_allowed
		// add domain to License
	}

	/** @test */
	public function domain_field_is_required()
	{
	}

	/** @test */
	public function domain_field_must_be_unique_for_license()
	{
	}

	/** @test */
	public function domain_field_must_exist_to_be_removed_from_a_license()
	{
	}

	/** @test */
	public function get_all_domains_of_a_license()
	{
		// get License from license_key
		// return License->domains
	}

	/** @test */
	public function license_key_field_is_required()
	{
	}

	/** @test */
	public function license_key_field_must_be_valid()
	{
	}

	/** @test */
	public function remove_a_domain_from_a_license()
	{
		// get License from license_key
		// verify domain exists in License
		// delete domain from License
		// return true
	}
}
