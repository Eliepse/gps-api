<?php

namespace App\Http\Controllers;

use App\Enums\TrackerStatus;
use App\Mercure\MercureManager;
use App\Models\Tracker;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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

		return ["user" => $user, "trackers" => $trackers];
	}
}