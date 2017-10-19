<?php

namespace App\Console\Commands;

use App\Software;
use Illuminate\Console\Command;

class SLMSoftwareCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'slm:software';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List all software';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$columns = ['name', 'slug', 'repository', 'status'];
		if( count( $software = app( Software::class )->get( $columns )->toArray() ) > 0 ) {
			return $this->table( $columns, $software );
		}
		$this->error( 'No software found' );
	}
}
