<?php

namespace App\Transformers;

use App\Software;
use League\Fractal\TransformerAbstract;

class SoftwareTransformer extends TransformerAbstract
{
	public function transform( Software $software )
	{
		return [
			'name' => (string) $software->name,
			'slug' => (string) $software->slug,
			'repository' => (string) $software->repository,
			'status' => (string) $software->status,
			'createdAt' => (string) $software->created_at,
			'updatedAt' => (string) $software->updated_at,
			'archivedAt' => (string) $software->deleted_at,
		];
	}
}
