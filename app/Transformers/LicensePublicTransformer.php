<?php

namespace App\Transformers;

use App\License;
use League\Fractal\TransformerAbstract;

class LicensePublicTransformer extends TransformerAbstract
{
	public function transform( License $license )
	{
		return [
			'createdAt' => (string) $license->created_at,
			'domainCount' => (int) $license->domains->count(),
			'domainLimit' => (int) $license->max_domains_allowed,
			'expiresAt' => (string) $license->expires_at,
			'renewedAt' => (string) $license->renewed_at,
			'renewedCount' => (int) $license->num_times_renewed,
			'revokedAt' => (string) $license->deleted_at,
			'status' => $license->status,
			'updatedAt' => (string) $license->updated_at,
		];
	}
}
