<?php

namespace App\Observers;

use App\Events\Tracker\TrackerStatusChanged;
use App\Models\Tracker;

class TrackerObserver
{
	/**
	 * Handle the Tracker "created" event.
	 *
	 * @param \App\Models\Tracker $tracker
	 *
	 * @return void
	 */
	public function created(Tracker $tracker)
	{
		//
	}


	/**
	 * Handle the Tracker "updated" event.
	 *
	 * @param \App\Models\Tracker $tracker
	 *
	 * @return void
	 */
	public function updated(Tracker $tracker)
	{
		TrackerStatusChanged::dispatchIf($tracker->status !== $tracker->getOriginal("status"), [$tracker]);
	}


	/**
	 * Handle the Tracker "deleted" event.
	 *
	 * @param \App\Models\Tracker $tracker
	 *
	 * @return void
	 */
	public function deleted(Tracker $tracker)
	{
		//
	}


	/**
	 * Handle the Tracker "restored" event.
	 *
	 * @param \App\Models\Tracker $tracker
	 *
	 * @return void
	 */
	public function restored(Tracker $tracker)
	{
		//
	}


	/**
	 * Handle the Tracker "force deleted" event.
	 *
	 * @param \App\Models\Tracker $tracker
	 *
	 * @return void
	 */
	public function forceDeleted(Tracker $tracker)
	{
		//
	}
}
