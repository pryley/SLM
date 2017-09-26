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
		]));
		$this->assertResponseStatus( 200 );
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
		]));
		$errors = $this->response->getData()->errors;
		$this->assertContains( 'The email field is required.', $errors->email );
		$this->assertContains( 'The first name field is required.', $errors->first_name );
		$this->assertContains( 'The last name field is required.', $errors->last_name );
		$this->assertContains( 'The software field is required.', $errors->software );
		$this->assertContains( 'The transaction id field is required.', $errors->transaction_id );
		$this->assertResponseStatus( 422 );
	}

	/** @test */
	public function email_field_must_be_valid()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'email' => 'invalid email',
		]));
		$this->assertContains( 'The email must be a valid email address.', $this->response->getData()->errors->email );
		$this->assertResponseStatus( 422 );
	}

	/** @test */
	public function software_field_must_exist_as_slug_in_software_table()
	{
		$this->auth()->post( '/v1/licenses', $this->validParams([
			'software' => 'invalid_software_slug',
		]));
		$this->assertContains( 'The selected software is invalid.', $this->response->getData()->errors->software );
		$this->assertResponseStatus( 422 );
	}

	/** @test */
	public function transaction_id_field_must_be_unique()
	{
		$software = factory( Software::class )->create();
		$data = $this->validParams([
			'software' => $software->slug,
			'transaction_id' => 'transaction_id',
		]);
		$this->auth()->post( '/v1/licenses', $data );
		$this->auth()->post( '/v1/licenses', $data );
		$this->assertContains( 'The transaction id has already been taken.', $this->response->getData()->errors->transaction_id );
		$this->assertResponseStatus( 422 );
	}

	/** @test */
	public function deactivate_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$this->auth()->post( '/v1/licenses/deactivate', [
			'license_key' => $license->license_key,
		]);
		$this->assertEquals( 'inactive', $this->response->getData()->data->status );
		$this->assertResponseStatus( 200 );
	}

	/** @test */
	public function delete_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$this->auth()->delete( '/v1/licenses/'.$license->license_key );
		$this->assertResponseStatus( 204 );
	}

	/** @test */
	public function get_all_licenses()
	{
		factory( License::class, 5 )->create();
		$this->auth()->get( '/v1/licenses' );
		$this->assertNotEmpty( $this->response->getData()->data );
		$this->assertResponseStatus( 200 );
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
		]);
		$this->assertResponseStatus( 200 );
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
		]);
		$data = $this->response->getData()->data;
		$this->assertTrue( Carbon::parse( $data->renewedAt )->isToday() );
		$this->assertTrue( Carbon::parse( $data->expiresAt )->isNextYear() );
		$this->assertEquals( 'active', $data->status );
		$this->assertEquals( 1, $data->numTimesRenewed );
		$this->assertEquals( 5, $data->maxDomainsAllowed );
		$this->assertResponseStatus( 200 );
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
		]);
		$this->assertContains( 'active', $this->response->getData()->data->status );
		$this->assertResponseStatus( 200 );
	}

	/** @test */
	public function revoke_a_license()
	{
		$license = factory( License::class )->create([
			'status' => 'active',
		]);
		$this->auth()->post( '/v1/licenses/revoke', [
			'license_key' => $license->license_key,
		]);
		$this->assertContains( 'revoked', $this->response->getData()->data->status );
		$this->assertResponseStatus( 200 );
	}
}
