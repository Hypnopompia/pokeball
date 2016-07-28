<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Validator;

use Log;

use App\Pokeball;

class PokeballController extends Controller
{
	public function __construct() {
	}

	public function listpokeballs(Request $request) {
		return response()->json([ 'ok' => true, 'trackers' => Tracker::where('enabled', true)->upToDate()->get()->keyBy('id') ]);
	}

	public function update(Request $request) {
		$validator = Validator::make($request->all(), [
			'deviceid' => 'required',
			'data' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['ok' => false, 'errors' => $validator->errors() ]);
		}

		$data = explode(",", $request->data);
		$lat = $data[0];
		$lon = $data[1];
		$batt = $data[2];

		$pokeball = Pokeball::where('deviceid', $request->deviceid)->first();

		if (!$pokeball) {
			$pokeball = new Pokeball;
			$pokeball->deviceid = $deviceid;
		}

		if ($lat != 'null') {
			$pokeball->latitude = $lat;
		}

		if ($lon != 'null') {
			$pokeball->longitude = $lon;
		}

		$pokeball->battery = $batt;
		$pokeball->name = $name;
		$pokeball->save();

		return response()->json([ 'ok' => true, 'pokeball' => $pokeball ]);
	}
}
