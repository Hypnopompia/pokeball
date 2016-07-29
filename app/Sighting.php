<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sighting extends Model
{
	use SoftDeletes;
	protected $table = 'sightings';
	protected $fillable = [];
	protected $hidden = ['deleted_at'];
	protected $appends = [];
	protected $dates = ['deleted_at'];

	protected $casts = [
		'id' => 'integer',
		'pokemon_id' => 'integer',
		'latitude' => 'string',
		'longitude' => 'string',
		'expires' => 'datetime'
	];

	public function pokemon() {
		return $this->belongsTo('App\Pokemon');
	}

	public function scopeNotexpired($query) {
		return $query->where('expires', '>', Carbon::now());
	}

	public function distanceFrom(Pokeball $pokeball, $unit = 'F') {
		$lat1 = $pokeball->latitude;
		$lon1 = $pokeball->longitude;
		$lat2 = $this->latitude;
		$lon2 = $this->longitude;


		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else if ($unit == "F") {
			return intval($miles * 5280);
		} else {
			return $miles;
		}
	}

	public static function search($pokemon_id, $latitude, $longitude, $expires) {
		return Sighting::where('pokemon_id', $pokemon_id)
			->where('latitude', $latitude)
			->where('longitude', $longitude)
			->where('expires', '>', Carbon::createFromTimestamp($expires - 10))
			->where('expires', '<', Carbon::createFromTimestamp($expires + 20))
			->first();
	}

	public static function add(Pokeball $pokeball, $pokemon_id, $latitude, $longitude, $expires) {
		$sighting = Sighting::search($pokemon_id, $latitude, $longitude, $expires);

		if ($sighting) {
			return ['new' => false, 'sighting' => $sighting];
		}

		$sighting = new Sighting;
		$sighting->pokemon_id = $pokemon_id;
		$sighting->pokeball_id = $pokeball->id;
		$sighting->latitude = $latitude;
		$sighting->longitude = $longitude;
		$sighting->expires = Carbon::createFromTimestamp($expires);
		$sighting->save();

		return ['new' => true, 'sighting' => $sighting];
	}
}
