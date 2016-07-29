<?php

namespace App\Console\Commands;

use App\Pokeball;
use App\Pokemon;
use App\Sighting;
use Illuminate\Console\Command;
use Log;

class Import extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$pokeball = Pokeball::find(1);
		if (!$pokeball) {
			echo "Pokeball not found.\n";
			return;
		}

		// insert into pokeballs set deviceid='340056000c51343334363138', latitude='40.29287412278603', longitude='-111.71147346496582', battery=100;

		$lat = $pokeball->latitude;
		$lon = $pokeball->longitude;

		$scan = json_decode(file_get_contents('https://pokevision.com/map/scan/' . $lat . '/' . $lon), true);

		if ($scan && $scan['status'] == "success") {
			$jobId = $scan['jobId'];
			$jobComplete = false;
			while (!$jobComplete) {
				$json = file_get_contents('https://pokevision.com/map/data/' . $lat . '/' . $lon . "/" . $jobId);
				$pokevision = json_decode($json, true);
				if ($pokevision && isset($pokevision['pokemon'])) {
					$jobComplete = true;
				}
				sleep(2);
			}
		}

		if (!isset($pokevision)) {
			Log::info("Pokevision doesn't seem to be working right now.");
			return;
		}

		Log::info("There are " . count($pokevision['pokemon']) . " pokemon nearby.");

		$imported = 0;
		$wiggle = false;
		foreach ($pokevision['pokemon'] as $pokemon) {
			$add = Sighting::add($pokeball, $pokemon['pokemonId'], $pokemon['latitude'], $pokemon['longitude'], $pokemon['expiration_time']);
			$sighting = $add['sighting'];

			if ($add['new']) {
				$distance = $sighting->distanceFrom($pokeball);
				Log::info("New " . $sighting->pokemon->name . ' in ' . $distance . ' feet.');

				if (Pokemon::find($pokemon['pokemonId'])->notify && $distance < 200) {
					$wiggle = true;
				}
				$imported++;
			}
		}
		Log::info($imported . " new sightings.");

		if ($wiggle) {
			$pokeball->wiggle();
		}

	}
}
