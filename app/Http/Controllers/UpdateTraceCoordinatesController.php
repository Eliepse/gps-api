<?php

namespace App\Http\Controllers;

use App\Http\Requests\GPSTraceRequest;
use App\Models\Coordinate;
use App\Models\Trace;
use App\Models\Tracker;

class UpdateTraceCoordinatesController
{
	public function __invoke(GPSTraceRequest $request, Trace $trace)
	{
		/** @var Tracker $tracker */
		$tracker = $request->user();

		if (! $trace->isRecording() || $trace->tracker_id !== $tracker->id) {
			return response(status: 403);
		}

		$coordinates = array_map(function ($coord) use ($trace) {
			return array_merge(Coordinate::newFromTrackerTraceCoordinates($coord)->toInsertQueryArray(), ["trace_id" => $trace->id]);
		}, $request->segment);

		$trace->coordinates()->insertOrIgnore($coordinates);
		$tracker->seen()->save();

		return response()->noContent();
	}
}