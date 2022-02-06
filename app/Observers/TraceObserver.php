<?php

namespace App\Observers;

use App\Enums\TraceStatus;
use App\Events\TraceStarted;
use App\Events\TraceStopped;
use App\Models\Trace;

class TraceObserver
{
	/**
	 * Handle the Trace "created" event.
	 *
	 * @param Trace $trace
	 *
	 * @return void
	 */
	public function created(Trace $trace)
	{
		TraceStarted::dispatchIf($trace->status === TraceStatus::Recording, $trace);
	}


	/**
	 * Handle the Trace "updated" event.
	 *
	 * @param Trace $trace
	 *
	 * @return void
	 */
	public function updated(Trace $trace)
	{
		TraceStopped::dispatchIf($trace->status === TraceStatus::Finished, $trace);
	}


	/**
	 * Handle the Trace "deleted" event.
	 *
	 * @param Trace $trace
	 *
	 * @return void
	 */
	public function deleted(Trace $trace)
	{
		//
	}


	/**
	 * Handle the Trace "restored" event.
	 *
	 * @param Trace $trace
	 *
	 * @return void
	 */
	public function restored(Trace $trace)
	{
		//
	}


	/**
	 * Handle the Trace "force deleted" event.
	 *
	 * @param Trace $trace
	 *
	 * @return void
	 */
	public function forceDeleted(Trace $trace)
	{
		//
	}
}
