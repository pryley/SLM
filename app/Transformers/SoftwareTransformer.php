<?php

namespace App\Transformers;

use App\Software;
use League\Fractal\TransformerAbstract;

class SoftwareTransformer extends TransformerAbstract
{
    public function transform(Software $software)
    {
        return [
            'archivedAt' => (string) $software->deleted_at,
            'createdAt' => (string) $software->created_at,
            'name' => (string) $software->name,
            'productId' => (string) $software->product_id,
            'repository' => (string) $software->repository,
            'status' => (string) $software->status,
            'updatedAt' => (string) $software->updated_at,
        ];
    }
}
