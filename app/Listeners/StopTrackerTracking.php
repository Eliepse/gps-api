<?php

namespace App\Listeners;

use App\Enums\TraceStatus;
use App\Events\TraceStopped;
use App\Notifications\tracker\StopTrackerNotification;

class StopTrackerTracking
{
	/**
	 * Handle the event.
	 */
	public function handle(TraceStopped $event)
	{
		if ($event->trace->status !== TraceStatus::Recording) {
			$event->trace->tracker->notifyNow(new StopTrackerNotification());
		}
	}
}
