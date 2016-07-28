<?php

use App\Pokemon;
use Illuminate\Database\Seeder;

class PokemonSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$pokedex = json_decode(file_get_contents(storage_path() . '/json/pokedex.json'), true);
		foreach ($pokedex as $id => $name) {
			$pokemon = new Pokemon;
			$pokemon->id = $id;
			$pokemon->name = $name;
			$pokemon->save();
		}
	}
}
