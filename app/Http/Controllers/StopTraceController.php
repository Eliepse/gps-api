<?php

namespace App\Http\Controllers;

use App\Enums\TraceStatus;
use App\Models\Trace;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StopTraceController extends \Illuminate\Routing\Controller
{
	public function __construct()
	{
		$this->middleware("user");
	}


	public function __invoke(Request $request, Trace $trace)
	{
		/** @var User $user */
		$user = $request->user();

		if ($trace->user_id !== $user->id) {
			abort(403);
		}

		if ($trace->status === TraceStatus::Finished) {
			abort(403, "Trace already finished.");
		}

		$trace->finished_at = Carbon::now();
		$trace->status = TraceStatus::Finished;
		$trace->save();


		return [
			"uid" => $trace->uid,
			"started_at" => $trace->started_at,
			"finished_at" => $trace->finished_at,
		];
	}
}