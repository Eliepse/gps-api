<?php

namespace App\Http\Controllers;

use App\Events\TraceCoordinatesUpdated;
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

		$coordinates = collect($request->segment)->map(function ($coord) use ($trace) {
			$coordinate = Coordinate::newFromTrackerTraceCoordinates($coord);
			$coordinate->trace_id = $trace->id;
			return $coordinate;
		});

		$trace->coordinates()->insertOrIgnore($coordinates->map(fn($coord) => $coord->toInsertQueryArray())->toArray());
		$tracker->seen()->save();

		broadcast(new TraceCoordinatesUpdated($trace, $coordinates))->toOthers();

		return response()->noContent();
	}
}