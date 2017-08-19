<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLicensesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('licenses', function (Blueprint $table) {
			$table->increments('id');
			$table->uuid('license_key')->index();
			$table->string('status');
			$table->string('first_name');
			$table->string('last_name');
			$table->string('email');
			$table->string('company_name')->nullable();
			$table->integer('max_domains_allowed')->default(1);
			$table->string('transaction_id')->unique();
			$table->timestamp('expires_at')->nullable();
			$table->timestamp('renewed_at')->nullable();
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
		Schema::dropIfExists('licenses');
	}
}
