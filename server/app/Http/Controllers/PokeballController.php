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
			'coreid' => 'required',
			'data' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['ok' => false, 'errors' => $validator->errors() ]);
		}

		$deviceid = $request->coreid;
		$data = explode(",", $request->data);
		$lat = floatval($data[0]);
		$lon = floatval($data[1]);
		$batt = $data[2];

		$pokeball = Pokeball::where('deviceid', $deviceid)->first();

		if (!$pokeball) {
			$pokeball = new Pokeball;
			$pokeball->deviceid = $deviceid;
		}

		if ($lat != 0) {
			$pokeball->latitude = $lat;
		}

		if ($lon != 0) {
			$pokeball->longitude = $lon;
		}

		$pokeball->battery = $batt;
		$pokeball->save();

		Log::info("Pokeball is at " . $lat . '/' . $lon . '. ' . $pokeball->battery . "% battery.");

		return response()->json([ 'ok' => true, 'pokeball' => $pokeball ]);
	}

    public function wiggle() {
    	Pokeball::find(1)->wiggle();
    	return ":D";
    }
}
