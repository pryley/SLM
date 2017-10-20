<?php

namespace App\Console\Commands;

use App\License;
use App\Software;
use App\Http\Controllers\DomainController;
use App\Exceptions\DomainLimitReachedException;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SLMCreateDomainCommand extends Command
{
	/**
	 * @var array
	 */
	protected $software;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:create-domain
		{--license : Enter the license that you are assigning this domain to}
		{--domain : Enter the domain URL}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add a new domain to a license';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle( DomainController $controller )
	{
		$request = new Request;
		$request->merge([
			'license_key' => $this->getDomainLicense(),
			'domain' => $this->getDomainUrl(),
		]);
		try {
			$response = $controller->store( $request )->getData();
			$this->line( sprintf( '<comment>Domain added to license: %s</comment>', $response->data->domain ));
		}
		catch( ValidationException $e ) {
			foreach( $e->validator->errors()->getMessages() as $key => $messages ) {
				foreach( $messages as $error ) {
					$this->error( $error );
				}
			}
		}
		catch( DomainLimitReachedException $e ) {
			$this->error( 'The domain limit has been reached for this license.' );
		}
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getDomainLicense()
	{
		return $this->output->ask( 'Enter the license that you are assigning this domain to', null, function( $value ) {
			return $this->validateInput( 'license', 'exists:licenses,license_key', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getDomainUrl()
	{
		return $this->output->ask( 'Enter the domain URL', null, function( $value ) {
			return $this->validateInput( 'domain', 'url', $value );
		});
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
