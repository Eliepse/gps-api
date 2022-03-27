<?php

namespace App\Http\Controllers\Trackers;

use App\Events\TrackerMetadataUpdated;
use App\Http\Requests\UpdateTrackerRequest;
use App\Models\Coordinate;
use App\Models\Trace;
use App\Models\Tracker;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class UpdateTrackerController extends Controller
{
	public function __construct()
	{
		$this->middleware("tracker");
	}


	public function __invoke(UpdateTrackerRequest $request): Response
	{
		/** @var Tracker $tracker */
		$tracker = $request->user();
		$tracker->seen()->save();

		TrackerMetadataUpdated::dispatch($tracker, $request->toArray());

		if (! $request->trace_id) {
			return response()->noContent();
		}

		/** @var ?Trace $trace */
		$trace = Trace::query()->find($request->trace_id);

		if (! $trace) {
			return response(status: 400);
		}

		if (! $trace->isRecording() || $trace->tracker_id !== $tracker->id) {
			return response(status: 403);
		}

		$coordinates = collect($request->coordinates)->map(function ($coord) use ($trace) {
			$coordinate = Coordinate::newFromTrackerTraceCoordinates($coord);
			$coordinate->trace_id = $trace->id;
			return $coordinate;
		});

		$trace->coordinates()->insertOrIgnore($coordinates->map(fn($coord) => $coord->toInsertQueryArray())->toArray());

//		Maybe not usefull as TrackerMetadataUpdated is already fired with coordinates
//		TraceCoordinatesUpdated::dispatch($trace, $coordinates);

		return response()->noContent();
	}
}