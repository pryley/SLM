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
			'transaction_id' => str_random( 32 ),
		], $overrides );
	}

	/** @test */
	public function create_a_license()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'product_id' => factory( Software::class )->create()->product_id,
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
			'product_id' => '',
			'transaction_id' => '',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'email' => ['The email field is required.'],
				'first_name' => ['The first name field is required.'],
				'last_name' => ['The last name field is required.'],
				'product_id' => ['The product id field is required when software is not present.'],
				'software.name' => ['The software.name field is required when product id is not present.'],
				'software.product_id' => ['The software.product id field is required when product id is not present.'],
				'software.slug' => ['The software.slug field is required when product id is not present.'],
				'transaction_id' => ['The transaction id field is required.'],
			],
		]);
	}

	/** @test */
	public function email_field_must_be_valid()
	{
		$software = factory( Software::class )->create();
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'product_id' => $software->product_id,
			'email' => 'invalid email',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'email' => ['The email must be a valid email address.']
			],
		]);
	}

	/** @test */
	public function software_field_must_exist_as_product_id_in_software_table()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'product_id' => 'invalid_software_product_id',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'product_id' => ['The selected product id is invalid.']
			],
		]);
	}

	/** @test */
	public function software_fields_must_exist_in_license_request()
	{
		$software = factory( Software::class )->create();
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => '',
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'product_id' => ['The product id field is required when software is not present.'],
				'software.name' => ['The software.name field is required when product id is not present.'],
				'software.product_id' => ['The software.product id field is required when product id is not present.'],
				'software.slug' => ['The software.slug field is required when product id is not present.'],
			],
		]);
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => [
				'name' => 'Product',
				'slug' => 'product',
			],
		]))->seeJson([
			'status' => 422,
			'errors' => (object) [
				'software.product_id' => ['The software.product id field is required when product id is not present.'],
			],
		]);
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'product_id' => $software->product_id,
			'software' => '',
		]))->seeJson([
			'status' => 200,
		]);
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => [
				'name' => 'New Product',
				'slug' => 'new-slug',
				'product_id' => '13',
			],
		]))->seeJson([
			'status' => 200,
		]);
	}

	/** @test */
	public function transaction_id_field_must_be_unique()
	{
		$software = factory( Software::class )->create();
		$data = $this->validParams([
			'product_id' => $software->product_id,
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
			'product_id' => $software->product_id,
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
		$this->assertEquals( 1, $data->renewedCount );
		$this->assertEquals( 5, $data->domainLimit );
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
