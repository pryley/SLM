<?php

namespace App\Observers;

use App\Domain;

class DomainObserver
{
	/**
	 * @return void
	 */
	public function created( Domain $domain )
	{
		\Log::debug( 'Domain created' );
	}

	/**
	 * @return void
	 */
	public function deleted( Domain $domain )
	{
		\Log::debug( 'Domain deleted' );
	}
}
