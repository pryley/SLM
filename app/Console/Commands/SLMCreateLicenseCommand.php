<?php

namespace App\Console\Commands;

use App\License;
use App\Software;
use App\Http\Controllers\LicenseController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SLMCreateLicenseCommand extends Command
{
	const COLUMNS = ['name', 'product_id', 'status'];

	/**
	 * @var array
	 */
	protected $software;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:create-license
		{--first_name : Enter the license owner\'s first name}
		{--last_name : Enter the license owner\'s last name}
		{--email : Enter the license owner\'s email}
		{--company : Enter the license owner\'s company name (optional)}
		{--product_id : Enter the software product ID assigned to this license}
		{--domains : Enter the maximum number of domains allowed for this license}
		{--transaction_id : Enter the transaction ID for this license}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new license';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle( LicenseController $controller )
	{
		if( empty( $this->getSoftware() )) {
			return $this->error( 'No software exists' );
		}
		$request = new Request;
		$request->merge([
			'product_id' => $this->getLicenseSoftware(),
			'first_name' => $this->getLicenseFirstName(),
			'last_name' => $this->getLicenseLastName(),
			'email' => $this->getLicenseEmail(),
			'company_name' => $this->getLicenseCompany(),
			'max_domains_allowed' => $this->getLicenseDomainsAllowed(),
			'transaction_id' => $this->getLicenseTransactionId(),
		]);
		try {
			$response = $controller->store( $request )->getData();
			$this->line( sprintf( '<comment>License created: %s</comment>', $response->data->license ));
		}
		catch( ValidationException $e ) {
			foreach( $e->validator->errors()->getMessages() as $key => $messages ) {
				foreach( $messages as $error ) {
					$this->error( $error );
				}
			}
		}
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseCompany()
	{
		return $this->output->ask( ' Enter the license owner\'s company name (optional)', null, function( $value ) {
			return $value;
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseDomainsAllowed()
	{
		return $this->output->ask( 'Enter the maximum number of domains allowed for this license', null, function( $value ) {
			return $this->validateInput( 'domains', 'integer|min:1', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseEmail()
	{
		return $this->output->ask( ' Enter the license owner\'s email', null, function( $value ) {
			return $this->validateInput( 'email', 'email', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseFirstName()
	{
		return $this->output->ask( 'Enter the license owner\'s first name', null, function( $value ) {
			return $this->validateInput( 'first_name', 'min:1', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseLastName()
	{
		return $this->output->ask( 'Enter the license owner\'s last name', null, function( $value ) {
			return $this->validateInput( 'last_name', 'min:1', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseSoftware()
	{
		$this->showSoftwareTable();
		return $this->output->ask( 'Enter the software product ID assigned to this license', null, function( $value ) {
			return $this->validateInput( 'product-id', 'exists:software,product_id', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getLicenseTransactionId()
	{
		return $this->output->ask( 'Enter the transaction ID for this license', null, function( $value ) {
			return $this->validateInput( 'transaction_id', 'unique:licenses,transaction_id', $value );
		});
	}

	/**
	 * @return array
	 */
	protected function getSoftware()
	{
		return $this->software = app( Software::class )->get( static::COLUMNS )->toArray();
	}

	/**
	 * @return void
	 */
	protected function showSoftwareTable()
	{
		$this->table( static::COLUMNS, $this->software );
	}

	/**
	 * @param string $attribute
	 * @param string $validation
	 * @param string $value
	 * @return string
	 * @throws Exception
	 */
	protected function validateInput( $attribute, $validation, $value )
	{
		if( 0 === strlen( $value )) {
			throw new \Exception( 'A value is required.' );
		}
		$validator = app( 'validator' )->make( [$attribute => $value], [$attribute => $validation] );
		if( $validator->fails() ) {
			throw new \Exception( $validator->errors()->first( $attribute ));
		}
		return $value;
	}
}
