<?php

namespace App\Observers;

use App\License;
use Ramsey\Uuid\Uuid;

class LicenseObserver
{
	/**
	 * @return void
	 */
	public function created( License $license )
	{
	}

	/**
	 * @return void
	 */
	public function creating( License $license )
	{
		$license->license_key = strtoupper( Uuid::uuid4()->toString() );
	}

	/**
	 * @return void
	 */
	public function deactivated( License $license )
	{
	}

	/**
	 * @return void
	 */
	public function removed( License $license )
	{
	}

	/**
	 * @return void
	 */
	public function renewed( License $license )
	{
	}

	/**
	 * @return void
	 */
	public function restored( License $license )
	{
	}

	/**
	 * @return void
	 */
	public function revoked( License $license )
	{
	}
}
