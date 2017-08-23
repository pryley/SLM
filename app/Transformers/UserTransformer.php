<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
	public function transform( User $user )
	{
		return [
			'id' => $user->uid,
			'username' => $user->username,
			'email' => $user->email,
			'role' => $user->role,
			'isActive' => (bool) $user->is_active,
			'createdAt' => (string) $user->created_at,
			'updatedAt' => (string) $user->updated_at,
		];
	}
}
