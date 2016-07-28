<?php

namespace App\Console\Commands;

use Log;

use App\Pokeball;
use App\Sighting;
use Illuminate\Console\Command;

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

		// $lat = "40.29287412278603";
		// $lon = "-111.71147346496582";

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
		foreach ($pokevision['pokemon'] as $pokemon) {
			if (Sighting::add($pokemon['pokemonId'], $pokemon['latitude'], $pokemon['longitude'], $pokemon['expiration_time'])) {
				$imported++;
			}
		}
		Log::info("Imported " . $imported . " new sightings.");
	}
}
