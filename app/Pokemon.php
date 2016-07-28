<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pokemon extends Model
{
	use SoftDeletes;
	protected $table = 'pokemon';
	protected $fillable = [];
	protected $hidden = ['deleted_at'];
	protected $appends = [];
	protected $dates = ['deleted_at'];

	protected $casts = [
		'id' => 'integer',
		'name' => 'string',
	];

	public function sightings() {
		return $this->hasMany('App\Sighting');
	}

	public function sightingsNotExpired() {
		return $this->hasMany('App\Sighting')->where('expires', '>', Carbon::now());
	}
}
