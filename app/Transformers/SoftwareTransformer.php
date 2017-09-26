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
			'status' => (string) $software->status,
			'createdAt' => (string) $license->created_at,
			'updatedAt' => (string) $license->updated_at,
			'archivedAt' => (string) $license->deleted_at,
		];
	}
}
