<?php

namespace App\Listeners;

use App\Enums\TraceStatus;
use App\Events\TraceStarted;
use App\Notifications\tracker\StartTrackerNotification;

class StartTrackerTracking
{
	/**
	 * Handle the event.
	 */
	public function handle(TraceStarted $event)
	{
		if ($event->trace->status !== TraceStatus::Recording) {
			return;
		}

		$event->trace->tracker->notifyNow(new StartTrackerNotification($event->trace));
	}
}
