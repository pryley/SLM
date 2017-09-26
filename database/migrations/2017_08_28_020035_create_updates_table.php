<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'updates', function( Blueprint $table ) {
			$table->increments( 'id' );
			$table->integer( 'software_id' )->unsigned();
			$table->string( 'version' );
			$table->timestamps();

			$table->foreign( 'software_id' )->references( 'id' )->on( 'software' )->onDelete( 'cascade' );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'updates' );
	}
}
