<?php

namespace App\Console\Commands;

use App\Pokeball;
use App\Pokemon;
use App\Sighting;
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

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

		$lat = $pokeball->latitude;
		$lon = $pokeball->longitude;

		$tenMinutesAgo = Carbon::now()->subMinutes(10);

		if ($lat == 0 || $lon == 0 || $pokeball->updated_at < $tenMinutesAgo) {
			return; // Don't know where the pokeball is or it hasn't checked in for a while.
		}

		$command = "node /home/ubuntu/pogonode/pokemon.js " . $lat . " " . $lon . " 2>&1";
		$json = shell_exec($command);

		// $json = '[{"encounterId":"6394711798519346173","pokemon":{"id":58,"name":"Growlithe"},"latitude":40.38698761751936,"longitude":-111.82048104150228,"expires":1.171}]';


		$encounters = json_decode($json, true);
		$imported = 0;

		if (is_array($encounters)) {
			foreach ($encounters as $encounter) {
				$sighting = Sighting::where('encounterid', $encounter['encounterId'])->first();
				if (!$sighting) { // New sighting!
					$sighting = new Sighting;
					$sighting->encounterid = $encounter['encounterId'];
					$sighting->pokemon_id = $encounter['pokemon']['id'];
					$sighting->pokeball_id = $pokeball->id;
					$sighting->latitude = $encounter['latitude'];
					$sighting->longitude = $encounter['longitude'];
					$sighting->expires = Carbon::createFromTimestamp(time() + intval($encounter['expires']));
					$sighting->save();

					$distance = $sighting->distanceFrom($pokeball);

					Log::info("New " . $sighting->pokemon->name . ' in ' . $distance . ' feet.');

					$imported++;
				}
			}

			Log::info($imported . " new sightings.");

			if ($imported > 0) {
				Log::info("New pokemon found. Go catch em!");
				$pokeball->wiggle();
			}

		} else {
			Log::error("node script failed.");
			Log::debug($json);
		}


	}
}
