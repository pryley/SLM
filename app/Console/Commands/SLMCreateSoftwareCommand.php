<?php

namespace App\Console\Commands;

use App\Http\Controllers\SoftwareController;
use App\Software;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SLMCreateSoftwareCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:create-software
		{--name : Enter the software name}
		{--slug : Enter the software slug}
		{--repository : Enter the software repository}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new software';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle( SoftwareController $controller )
	{
		$request = new Request;
		$request->merge([
			'name' => $this->getSoftwareName(),
			'slug' => $this->getSoftwareSlug(),
			'repository' => $this->getSoftwareRepository(),
		]);
		try {
			$controller->store( $request );
			$this->line( '<comment>Software created</comment>' );
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
	protected function getSoftwareName()
	{
		return $this->output->ask( 'Enter the software name', null, function( $value ) {
			return $this->validateInput( 'name', 'unique:software', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getSoftwareRepository()
	{
		return $this->output->ask( 'Enter the software repository', null, function( $value ) {
			return $this->validateInput( 'repository', 'url', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getSoftwareSlug()
	{
		return $this->output->ask( 'Enter the software slug', null, function( $value ) {
			return $this->validateInput( 'slug', 'alpha_dash|unique:software', $value );
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
