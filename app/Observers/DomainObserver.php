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
	}

	/**
	 * @return void
	 */
	public function deleted( Domain $domain )
	{
	}
}
