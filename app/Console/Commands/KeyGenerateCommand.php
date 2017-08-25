<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class KeyGenerateCommand extends Command
{
	/**
     * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'key:generate {--show : Show the generated Application key instead of modifying files}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Set the Application key';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$key = Str::random( 32 );
		if( $this->option( 'show' )) {
			return $this->line( '<comment>Application key:</comment> ' . $key );
		}
		$path = base_path( '.env' );
		if( file_exists( $path )) {
			file_put_contents( $path, str_replace( 'APP_KEY=' . env('APP_KEY'), 'APP_KEY=' . $key, file_get_contents( $path )));
		}
		$this->laravel['config']['app.key'] = $key;
		$this->info( 'Application key set successfully.' );
	}
}
