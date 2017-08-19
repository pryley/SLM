<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table( 'users' )->insert([
			'name' => 'paul',
			'email' => 'paul@geminilabs.io',
			'created_at' => Carbon::now(),
		]);
	}
}
