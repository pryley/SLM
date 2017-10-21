<?php

namespace App\Transformers;

use App\License;
use League\Fractal\TransformerAbstract;

class LicenseTransformer extends TransformerAbstract
{
	public function transform( License $license )
	{
		return [
			'company' => (string) $license->company_name,
			'createdAt' => (string) $license->created_at,
			'domainCount' => (int) $license->domains->count(),
			'domainLimit' => (int) $license->max_domains_allowed,
			'email' => $license->email,
			'expiresAt' => (string) $license->expires_at,
			'firstName' => $license->first_name,
			'lastName' => $license->last_name,
			'license' => $license->license_key,
			'renewedAt' => (string) $license->renewed_at,
			'renewedCount' => (int) $license->num_times_renewed,
			'revokedAt' => (string) $license->deleted_at,
			'status' => $license->status,
			'transactionId' => $license->transaction_id,
			'updatedAt' => (string) $license->updated_at,
		];
	}
}
