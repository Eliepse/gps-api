<?php

namespace App\Listeners;

use App\Enums\TrackerStatus;
use App\Events\TrackerStatusChanged;
use App\Notifications\tracker\ShutdownTrackerNotification;

class ShutdownTracker
{
	/**
	 * Handle the event.
	 */
	public function handle(TrackerStatusChanged $event)
	{
		if ($event->tracker->status !== TrackerStatus::Unavailable) {
			return;
		}

		$event->tracker->notifyNow(new ShutdownTrackerNotification());
	}
}
