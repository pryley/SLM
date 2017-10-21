<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
	public function transform( User $user )
	{
		return [
			'createdAt' => (string) $user->created_at,
			'email' => $user->email,
			'id' => $user->uuid,
			'role' => $user->role,
			'updatedAt' => (string) $user->updated_at,
		];
	}
}
