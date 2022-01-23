<?php

namespace App\Listeners;

use App\Enums\TrackerStatus;
use App\Events\Tracker\TrackerStatusChanged;
use App\Notifications\ControlTracker;

class SendActionToTracker
{
	public string $tracker_uid;
	public string $action;


	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}


	/**
	 * Handle the event.
	 *
	 * @param \App\Events\Tracker\TrackerStatusChanged $event
	 *
	 * @return void
	 */
	public function handle(TrackerStatusChanged $event)
	{
		$action = match ($event->tracker->status) {
			TrackerStatus::Tracking => "start",
			TrackerStatus::Unavailable => "shutdown",
			default => "idle",
		};

		$event->tracker->notifyNow(new ControlTracker($action));
	}


}
