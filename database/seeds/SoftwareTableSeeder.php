<?php

use App\Software;
use Illuminate\Database\Seeder;

class SoftwareTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		factory( Software::class )->create([
			'name' => 'Site Reviews - Tripadvisor',
			'product_id' => 'site-reviews-tripadvisor',
			'repository' => 'https://bitbucket.org/geminilabs/site-reviews-tripadvisor',
			'status' => 'active',
		]);
	}
}
