<?php

namespace App\Listeners;

use App\Enums\TrackerStatus;
use App\Events\TraceStarted;

class ChangeTrackerStatus
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}


	/**
	 * Handle the event.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function handle(TraceStarted $event)
	{
		$tracker = $event->trace->tracker;

		if (! $tracker->isBanned() && $tracker->registered) {
			$tracker->status = TrackerStatus::Tracking;
			$tracker->save();
		}
	}
}
