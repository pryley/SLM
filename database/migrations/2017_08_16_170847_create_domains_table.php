<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'domains', function( Blueprint $table ) {
			$table->increments( 'id' );
			$table->integer( 'license_id' )->unsigned();
			$table->string( 'domain' );
			$table->timestamps();

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
		Schema::dropIfExists( 'domains' );
	}
}
