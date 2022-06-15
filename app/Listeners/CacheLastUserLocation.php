<?php

namespace App\Listeners;

use App\Events\TrackerMetadataUpdated;
use Illuminate\Support\Facades\Cache;

class CacheLastUserLocation
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
	 * @param TrackerMetadataUpdated $event
	 *
	 * @return void
	 */
	public function handle(TrackerMetadataUpdated $event)
	{
		if (empty($event->coordinate)) {
			return;
		}

		Cache::put("tracker:{$event->tracker->id}:last-location", $event->coordinate);
	}
}
