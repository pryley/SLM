<?php

namespace App\Transformers;

use App\Domain;
use League\Fractal\TransformerAbstract;

class DomainTransformer extends TransformerAbstract
{
    public function transform(Domain $domain)
    {
        return [
            'createdAt' => (string) $domain->created_at,
            'domain' => $domain->domain,
            'updatedAt' => (string) $domain->updated_at,
        ];
    }
}
