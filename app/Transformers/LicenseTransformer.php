<?php

namespace App\Transformers;

use App\License;
use League\Fractal\TransformerAbstract;

class LicenseTransformer extends TransformerAbstract
{
	public function transform( License $license )
	{
		return [
			'license' => $license->license_key,
			'status' => $license->status,
			'firstName' => $license->first_name,
			'lastName' => $license->last_name,
			'email' => $license->email,
			'company' => (string) $license->company_name,
			'transactionId' => $license->transaction_id,
			'maxDomainsAllowed' => (int) $license->max_domains_allowed,
			'numTimesRenewed' => (int) $license->num_times_renewed,
			'expiresAt' => (string) $license->expires_at,
			'createdAt' => (string) $license->created_at,
			'updatedAt' => (string) $license->updated_at,
			'renewedAt' => (string) $license->renewed_at,
			'revokedAt' => (string) $license->deleted_at,
		];
	}
}
