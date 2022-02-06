<?php

namespace App\Http\Controllers;

use App\Enums\TraceStatus;
use App\Models\Trace;
use App\Models\Tracker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateTraceController extends \Illuminate\Routing\Controller
{
	public function __construct()
	{
		$this->middleware("user");
	}


	public function __invoke(Request $request)
	{
		$request->validate(["tracker_uid" => "exists:trackers,uid"]);

		/** @var User $user */
		$user = $request->user();

		/** @var Tracker $tracker */
		$tracker = $user->trackers()->where("uid", $request->tracker_uid)->first();

		if (! $tracker) {
			abort(403, "You are not allowed to connect to this tracker.");
		}

		if ($user->traces()->where("status", "!=", TraceStatus::Finished)->exists()) {
			abort(403, "You already have an unfinished trace.");
		}

		/** @var Trace $trace */
		$trace = $user->traces()->create([
			"uid" => Str::uuid(),
			"tracker_id" => $tracker->id,
			"started_at" => Carbon::now(),
			"status" => TraceStatus::Recording,
		]);

		return [
			"uid" => $trace->uid,
			"started_at" => $trace->started_at,
		];
	}
}