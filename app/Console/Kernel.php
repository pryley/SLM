<?php

namespace App\Console;

use App\Console\Commands\KeyGenerateCommand;
use App\Console\Commands\SLMAccessTokenCommand;
use App\Console\Commands\SLMClientsCommand;
use App\Console\Commands\SLMInstallCommand;
use App\Console\Commands\SLMUsersCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		KeyGenerateCommand::class,
		SLMAccessTokenCommand::class,
		SLMClientsCommand::class,
		SLMInstallCommand::class,
		SLMUsersCommand::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		//
	}
}
