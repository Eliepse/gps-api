<?php

namespace App\Observers;

use App\Models\Trace;

class TraceObserver
{
    /**
     * Handle the Trace "created" event.
     *
     * @param  \App\Models\Trace  $trace
     * @return void
     */
    public function created(Trace $trace)
    {
        //
    }

    /**
     * Handle the Trace "updated" event.
     *
     * @param  \App\Models\Trace  $trace
     * @return void
     */
    public function updated(Trace $trace)
    {
        //
    }

    /**
     * Handle the Trace "deleted" event.
     *
     * @param  \App\Models\Trace  $trace
     * @return void
     */
    public function deleted(Trace $trace)
    {
        //
    }

    /**
     * Handle the Trace "restored" event.
     *
     * @param  \App\Models\Trace  $trace
     * @return void
     */
    public function restored(Trace $trace)
    {
        //
    }

    /**
     * Handle the Trace "force deleted" event.
     *
     * @param  \App\Models\Trace  $trace
     * @return void
     */
    public function forceDeleted(Trace $trace)
    {
        //
    }
}
