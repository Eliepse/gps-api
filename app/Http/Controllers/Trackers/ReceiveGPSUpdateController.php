<?php

namespace App\Http\Controllers\Trackers;

use App\Http\Requests\GPSTraceRequest;
use App\Models\Trace;
use App\Models\Tracker;

class ReceiveGPSUpdateController
{
	public function __invoke(GPSTraceRequest $request, Trace $path)
	{
		/** @var Tracker $tracker */
		$tracker = $request->user();

		if ($path->tracker_id !== $tracker->id) {
			return response(status: 403);
		}

//		$request->trace
	}
}