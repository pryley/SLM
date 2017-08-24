<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create( 'users', function( Blueprint $table ) {
			$table->increments( 'id' );
			$table->uuid( 'uuid' )->unique()->index();
			$table->string( 'username' );
			$table->string( 'email' )->unique();
            $table->string( 'password' )->nullable();
			$table->enum( 'role', ['BASIC_USER', 'ADMIN_USER'] )->default( 'BASIC_USER' );
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
		Schema::dropIfExists( 'users' );
	}
}
