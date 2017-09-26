<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoftwareTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'software', function( Blueprint $table ) {
			$table->increments( 'id' );
			$table->string( 'name' )->unique();
			$table->string( 'slug' )->unique()->index();
			$table->string( 'repository' )->unique();
			$table->string( 'status' );
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'software' );
	}
}
