<?php

namespace App\Transformers;

use App\Domain;
use League\Fractal\TransformerAbstract;

class DomainTransformer extends TransformerAbstract
{
	public function transform( Domain $domain )
	{
		return [
			'domain' => $domain->domain,
			'createdAt' => (string) $domain->created_at,
			'updatedAt' => (string) $domain->updated_at,
		];
	}
}
