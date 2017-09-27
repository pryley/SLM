<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class SLMUsersCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:users';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all of the users';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$columns = ['id', 'email', 'role'];
		if( count( $users = app( User::class )->get( $columns )->toArray() ) > 0 ) {
			return $this->table( $columns, $users );
		}
		$this->error( 'No users found' );
	}
}
