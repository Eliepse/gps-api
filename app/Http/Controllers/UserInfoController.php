<?php

namespace App\Http\Controllers;

use App\Enums\TraceStatus;
use App\Enums\TrackerStatus;
use App\Mercure\MercureManager;
use App\Models\Coordinate;
use App\Models\Trace;
use App\Models\Tracker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class UserInfoController extends Controller
{
	public function __construct()
	{
		$this->middleware("user");
	}


	public function __invoke(Request $request, MercureManager $mercure): array
	{
		/** @var User $user */
		$user = $request->user();
		$trackers = $user->trackers()->where("status", "!=", TrackerStatus::Banned)->get();
		$subscribers = $mercure->fetchSubscriptions("/user/$user->id/trackers/{tracker}");

		$activeTrackersId = array_map(fn($sub) => $sub["payload"]["id"], $subscribers);
		$trackers = $trackers->map(fn(Tracker $tracker) => [...$tracker->toArray(), "active" => in_array($tracker->getMercureId(), $activeTrackersId)]);

		/** @var Trace $activeTrace */
		$activeTrace = $user->traces()->where("status", TraceStatus::Recording)->orderByDesc("id")->first();


		if ($activeTrace) {
			return [
				"user" => $user,
				"trackers" => $trackers,
				"trace" => [
					...$activeTrace->toArray(),
					"coordinates" => Coordinate::query()->select(["location"])->where("trace_id", $activeTrace->id)->orderBy("id")->get()->map(fn(Coordinate $coord) => $coord->getLatLng()),
				],
			];
		}

		$totalTravelled = DB::table("traces")->where("user_id", $user->id)->sum("length");

		return ["user" => array_merge($user->toArray(), [
			"stats" => [
				"totalTravelled" => $totalTravelled,
			],
		]), "trackers" => $trackers];
	}
}