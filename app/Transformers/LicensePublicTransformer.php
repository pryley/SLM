<?php

namespace App\Transformers;

use App\License;
use League\Fractal\TransformerAbstract;

class LicensePublicTransformer extends TransformerAbstract
{
	public function transform( License $license )
	{
		return [
			'license' => $license->license_key,
			'maxDomainsAllowed' => (int) $license->max_domains_allowed,
			'numTimesRenewed' => (int) $license->num_times_renewed,
			'status' => $license->status,
			'createdAt' => (string) $license->created_at,
			'expiresAt' => (string) $license->expires_at,
			'renewedAt' => (string) $license->renewed_at,
			'revokedAt' => (string) $license->deleted_at,
			'updatedAt' => (string) $license->updated_at,
		];
	}
}
