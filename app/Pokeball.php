<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Log;
use Carbon\Carbon;

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

	public function wiggle() {
		Log::info("Send wiggle.");

		$url = "https://api.particle.io/v1/devices/" . $this->deviceid . "/wiggle";
		$ch = curl_init( $url );

		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, array('arg' => 'on') );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . config('app.particleApiKey')) );

		$response = curl_exec( $ch );
	}
}
