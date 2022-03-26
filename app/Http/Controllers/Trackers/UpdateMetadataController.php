<?php

namespace App\Http\Controllers\Trackers;

use App\Events\TrackerMetadataUpdated;
use App\Http\Requests\TrackerMetadataRequest;
use App\Models\Tracker;
use Illuminate\Routing\Controller;

class UpdateMetadataController extends Controller
{
	public function __construct()
	{
		$this->middleware("tracker");
	}


	public function __invoke(TrackerMetadataRequest $request)
	{
		/** @var Tracker $tracker */
		$tracker = $request->user();
		$tracker->seen()->save();
		TrackerMetadataUpdated::dispatch($tracker, $request->toArray());
	}
}