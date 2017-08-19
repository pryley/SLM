<?php

use App\Domain;
use App\License;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class DomainTest extends TestCase
{
	use DatabaseTransactions;

	public $request;

	public function __construct()
	{
		$this->request = [
			'domain' => 'geminilabs.io',
			'license_key' => '',
		];
	}

	/** @test */
	public function add_a_domain_to_a_license()
	{
		// get License from license_key
		$response = $this->post( '/v1/domains/add', $this->request )->response;

		// get License domains
		$domain = $this->getDomainFromLicense( $response->original );

		// verify domain does not exist in License
		// verify License has not reached max_domains_allowed
		// add domain to License
	}

	/** @test */
	public function get_all_domains_of_a_license()
	{
		// get License from license_key
		// return License->domains
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
