<?php

namespace Tests\Feature;

use App\License;
use App\Software;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManageLicensesTest extends TestCase
{
	use DatabaseTransactions;

	private function validParams( $overrides = [] )
	{
		return array_merge([
			'email' => 'jane@doe.com',
			'first_name' => 'Jane',
			'last_name' => 'Doe',
			'max_domains_allowed' => 1,
			'software' => '',
			'transaction_id' => str_random( 32 ),
		], $overrides );
	}

	/** @test */
	public function create_a_license()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => factory( Software::class )->create()->slug,
		]))->seeJson([
			'status' => 200,
		]);
	}

	/** @test */
	public function validate_required_fields()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'software' => '',
			'transaction_id' => '',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'email' => ['The email field is required.'],
				'first_name' => ['The first name field is required.'],
				'last_name' => ['The last name field is required.'],
				'software' => ['The software field is required.'],
				'transaction_id' => ['The transaction id field is required.'],
			],
		]);
	}

	/** @test */
	public function email_field_must_be_valid()
	{
		$software = factory( Software::class )->create();
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => $software->slug,
			'email' => 'invalid email',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'email' => ['The email must be a valid email address.']
			],
		]);
	}

	/** @test */
	public function software_field_must_exist_as_slug_in_software_table()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => 'invalid_software_slug',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'software' => ['The selected software is invalid.']
			],
		]);
	}

	/** @test */
	public function transaction_id_field_must_be_unique()
	{
		$software = factory( Software::class )->create();
		$data = $this->validParams([
			'software' => $software->slug,
			'transaction_id' => 'transaction_id',
		]);
		$this->auth()->post( '/v1/licenses', $data )->seeJson([
			'status' => 200,
		]);
		$this->auth()->post( '/v1/licenses', $data )->seeJson([
			'status' => 422,
			'errors' => (object) [
				'transaction_id' => ['The transaction id has already been taken.']
			],
		]);
	}

	/** @test */
	public function deactivate_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$this->auth()->post( '/v1/licenses/deactivate', [
			'license_key' => $license->license_key,
		])->seeJson([
			'status' => 200,
		]);
		$this->assertEquals( 'inactive', $this->response->getData()->data->status );
	}

	/** @test */
	public function delete_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$this->auth()->delete( '/v1/licenses/'.$license->license_key )->seeJson([
			'status' => 204,
		]);
	}

	/** @test */
	public function get_all_licenses()
	{
		factory( License::class, 5 )->create();
		$this->auth()->get( '/v1/licenses' )->seeJson([
			'status' => 200,
		]);
		$this->assertNotEmpty( $this->response->getData()->data );
	}

	/** @test */
	public function query_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$software = factory( Software::class )->create();
		$software->licenses()->attach( $license->id );
		$this->post( '/v1/licenses/verify', [
			'license_key' => $license->license_key,
			'software' => $software->slug,
		])->seeJson([
			'status' => 200,
		]);
	}

	/** @test */
	public function renew_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'inactive',
			'expires_at' => Carbon::yesterday(),
			'max_domains_allowed' => 1,
		]);
		$this->auth()->post( '/v1/licenses/renew', [
			'license_key' => $license->license_key,
			'max_domains_allowed' => 5,
		])->seeJson([
			'status' => 200,
		]);
		$data = $this->response->getData()->data;
		$this->assertTrue( Carbon::parse( $data->renewedAt )->isToday() );
		$this->assertTrue( Carbon::parse( $data->expiresAt )->isNextYear() );
		$this->assertEquals( 'active', $data->status );
		$this->assertEquals( 1, $data->numTimesRenewed );
		$this->assertEquals( 5, $data->maxDomainsAllowed );
	}

	/** @test */
	public function restore_a_revoked_license()
	{
		$license = factory( License::class )->create([
			'status' => 'revoked',
			'deleted_at' => Carbon::now(),
		]);
		$this->auth()->post( '/v1/licenses/restore', [
			'license_key' => $license->license_key,
		])->seeJson([
			'status' => 200,
		]);
		$this->assertContains( 'active', $this->response->getData()->data->status );
	}

	/** @test */
	public function revoke_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$this->auth()->post( '/v1/licenses/revoke', [
			'license_key' => $license->license_key,
		])->seeJson([
			'status' => 200,
		]);
		$this->assertContains( 'revoked', $this->response->getData()->data->status );
	}
}
