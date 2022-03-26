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
		$trackerUids = $user->trackers()->where("status", "!=", TrackerStatus::Banned)->get("uid");
		$subscribers = $mercure->fetchSubscriptions("/user/$user->id/trackers/{tracker}");

		$activeTrackers = array_filter($subscribers, function ($sub) use ($trackerUids) {
			$payload = $sub["payload"] ?? [];

			if ($payload["type"] ?? null !== Tracker::class) {
				return false;
			}

			if (! $trackerUids->contains("uid", $payload["id"] ?? null)) {
				return false;
			}

			return true;
		});

		return [
			"user" => $user,
			"activeTrackers" => array_map(fn($tracker) => $tracker["payload"]["id"], $activeTrackers),
			"trackers" => [],
		];
	}
}