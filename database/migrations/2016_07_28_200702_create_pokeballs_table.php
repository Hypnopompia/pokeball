<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePokeballsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pokeballs', function (Blueprint $table) {
			$table->increments('id');
			$table->boolean('enabled')->default(false)->index();
			$table->string('deviceid')->unique();
			$table->string('latitude');
			$table->string('longitude');
			$table->float('battery');

			$table->softDeletes();
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('pokeballs');
	}
}
