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
			return false;
		}

		$sighting = new Sighting;
		$sighting->pokemon_id = $pokemon_id;
		$sighting->pokeball_id = $pokeball->id;
		$sighting->latitude = $latitude;
		$sighting->longitude = $longitude;
		$sighting->expires = Carbon::createFromTimestamp($expires);
		$sighting->save();

		return true;
	}
}
