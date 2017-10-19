<?php

namespace App\Console\Commands;

use App\User;
use App\Http\Controllers\UserController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SLMCreateUserCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:create-user
		{--email : Enter the user\'s email}
		{--password : Enter the user\'s password}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new user';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle( UserController $controller )
	{
		$request = new Request;
		$request->merge([
			'email' => $this->getEmail(),
			'password' => $this->getPassword(),
		]);
		try {
			$controller->store( $request );
			$this->line( '<comment>User created</comment>' );
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
	protected function getEmail()
	{
		return $this->output->ask( 'Enter the user\'s email', null, function( $value ) {
			return $this->validateInput( 'email', 'email|unique:users', $value );
		});
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	protected function getPassword()
	{
		return $this->output->ask( 'Enter the user\'s password', null, function( $value ) {
			return $this->validateInput( 'password', 'min:8', $value );
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
