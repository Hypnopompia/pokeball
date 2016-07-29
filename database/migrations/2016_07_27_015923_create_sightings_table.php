<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSightingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sightings', function (Blueprint $table) {
			$table->increments('id');

			$table->integer('pokemon_id');
			$table->integer('pokeball_id');
			$table->string('latitude');
			$table->string('longitude');

			$table->datetime('expires');

			$table->softDeletes();
			$table->nullableTimestamps();

			$table->index(['pokemon_id', 'latitude', 'longitude']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('sightings');
	}
}
