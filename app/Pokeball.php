<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
