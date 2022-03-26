<?php

namespace App\Http\Controllers;

use App\Enums\TraceStatus;
use App\Models\Trace;
use App\Models\Tracker;
use App\Models\User;
use Illuminate\Http\Request;

class GetLiveDataController extends \Illuminate\Routing\Controller
{
	public function __invoke(Request $request)
	{
		$isTracker = is_a($request->user(), Tracker::class);
		$isUser = is_a($request->user(), User::class);

		if (is_a($request->user(), Tracker::class)) {
			/** @var Tracker $tracker */
			$tracker = $request->user();
			$user = $tracker->user;
		} else {
			/** @var User $user */
			$user = $request->user();
		}

		$query = $user->traces()->whereIn("status", [TraceStatus::Recording, TraceStatus::Pause]);

		if ($isUser) {
			$query->with("coordinates");
		}

		/** @var ?Trace $trace */
		$trace = $query->first();

		if (! $trace) {
			return response()->noContent();
		}

		if ($isTracker) {
			return ["trace" => ["id" => $trace->id, "uid" => $trace->uid]];
		}

		return [
			"trace" => [
				"uid" => $trace->uid,
				"started_at" => $trace->started_at,
			],
			"path" => $trace->coordinates->toArray(),
		];
	}
}