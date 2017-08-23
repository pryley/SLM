<?php

namespace App\Providers;

use App\Domain;
use App\License;
use App\User;
use App\Observers\DomainObserver;
use App\Observers\LicenseObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Domain::observe( DomainObserver::class );
		License::observe( LicenseObserver::class );
		User::observe( UserObserver::class );
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
