<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
	public function transform( User $user )
	{
		return [
			'id' => $user->uuid,
			'email' => $user->email,
			'role' => $user->role,
			'createdAt' => (string) $user->created_at,
			'updatedAt' => (string) $user->updated_at,
		];
	}
}
