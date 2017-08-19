<?php

use App\License;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class LicenseTest extends TestCase
{
	use DatabaseTransactions;

	public $request;

	public function __construct()
	{
		$this->request = [
			'first_name' => 'Paul',
			'last_name' => 'Ryley',
			'email' => 'paul@geminilabs.io',
			'max_domains_allowed' => 1,
			'transaction_id' => '1234asdf',
		];
	}

	/** @test */
	public function activate_a_license()
	{}

	/** @test */
	public function add_a_domain_for_a_license()
	{
		$licenseKey = $this->post( '/v1/create', $this->request )->response->original->license_key;
		$license = app( License::class )->where( 'license_key', $licenseKey )->first();

		$this->post( 'v1/domains/add', [
			'license_key' => $licenseKey,
			'domain' => 'geminilabs.io',
		]);



		dd($license->domains);

		$this->assertEquals( 200, $request->status() );

	}

	/** @test */
	public function create_a_license()
	{
		$this->call( 'POST', '/v1/create', $this->request );
		$this->assertResponseStatus(200);

		$this->call( 'POST', '/v1/create', $this->request );
		$this->assertResponseStatus(422);
	}

	/** @test */
	public function deactivate_a_license()
	{
		// assign
		// act
		// assert
	}

	/** @test */
	public function delete_a_license()
	{}

	/** @test */
	public function query_a_license()
	{}

	/** @test */
	public function restore_a_license()
	{}

	/** @test */
	public function revoke_a_license()
	{}
}
