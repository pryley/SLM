<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoftwareLicensesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'software_licenses', function( Blueprint $table ) {
			$table->integer( 'software_id' )->unsigned();
			$table->integer( 'license_id' )->unsigned();

			$table->foreign( 'software_id' )->references( 'id' )->on( 'software' );
			$table->foreign( 'license_id' )->references( 'id' )->on( 'licenses' )->onDelete( 'cascade' );
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists( 'software_licenses' );
	}
}
