<?php

namespace App;

use App\Sighting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class Pokeball extends Model {
	use SoftDeletes;

	protected $table = 'pokeballs';
	protected $fillable = [];
	protected $hidden = ['deleted_at'];
	protected $dates = ['deleted_at'];

	protected $casts = [
		'id' => 'integer',
		'enabled' => 'boolean',
		'battery' => 'float',
		'latitude' => 'float',
		'longitude' => 'float',
	];

	public function scan() {
		$tenMinutesAgo = Carbon::now()->subMinutes(10);

		if ($this->latitude == 0 || $this->longitude == 0 || $this->updated_at < $tenMinutesAgo) {
			return; // Don't know where the pokeball is or it hasn't checked in for a while.
		}

		$command = "node /home/ubuntu/pogonode/pokemon.js " . $this->latitude . " " . $this->longitude . " 2>&1";
		Log::debug($command);

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
					$sighting->pokeball_id = $this->id;
					$sighting->latitude = $encounter['latitude'];
					$sighting->longitude = $encounter['longitude'];
					$sighting->expires = Carbon::createFromTimestamp(time() + intval($encounter['expires']));
					$sighting->save();

					$distance = $sighting->distanceFrom($this);

					Log::info("New " . $sighting->pokemon->name . ' in ' . $distance . ' feet.');

					$imported++;
				}
			}

			Log::info($imported . " new sightings.");

			if ($imported > 0) {
				Log::info("New pokemon found. Go catch em!");
				$this->wiggle();
			}

		} else {
			Log::error("node script failed.");
			Log::debug($json);
		}

	}

	public function wiggle() {
		Log::info("Sending wiggle command.");

		$url = "https://api.particle.io/v1/devices/" . $this->deviceid . "/wiggle";
		$ch = curl_init( $url );

		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, array('arg' => 'on') );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . config('app.particleApiKey')) );

		$response = curl_exec( $ch );

		Log::debug($response);
	}
}
