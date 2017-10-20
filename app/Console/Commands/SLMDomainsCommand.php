<?php

namespace App\Console\Commands;

use App\Http\Controllers\DomainController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SLMDomainsCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:domains
		{--license : Enter the license of the domains you want to see}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all domains of a license';

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
		]);
		try {
			$response = $controller->index( $request )->getData();
			if( !empty( $response->data )) {
				return $this->table( ['domain'], [$response->data] );
			}
			$this->error( 'This license has no domains assigned.' );
		}
		finally {}
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
