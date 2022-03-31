<?php

namespace App\Http\Controllers;

use App\Enums\TraceStatus;
use App\Http\Requests\CreateTraceRequest;
use App\Models\Trace;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CreateTraceController extends \Illuminate\Routing\Controller
{
	public function __construct()
	{
		$this->middleware("user");
	}


	public function __invoke(CreateTraceRequest $request): array
	{
		/** @var User $user */
		$user = $request->user();

		if (! $request->tracker) {
			abort(403, "You are not allowed to connect to this tracker.");
		}

		if ($user->traces()->where("status", "!=", TraceStatus::Finished)->exists()) {
			abort(403, "You already have an unfinished trace.");
		}

		/** @var Trace $trace */
		$trace = $user->traces()->create([
			"uid" => Str::uuid(),
			"tracker_id" => $request->tracker->id,
			"started_at" => Carbon::now(),
			"status" => TraceStatus::Recording,
		]);

		return [
			"uid" => $trace->uid,
			"tracker_uid" => $request->tracker->uid,
			"started_at" => $trace->started_at,
			"status" => TraceStatus::Recording,
		];
	}
}