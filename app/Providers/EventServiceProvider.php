<?php

namespace App\Providers;

use App\Events\TraceStarted;
use App\Events\TraceStopped;
use App\Events\TrackerStatusChanged;
use App\Listeners\ShutdownTracker;
use App\Listeners\StartTrackerTracking;
use App\Listeners\StopTrackerTracking;
use App\Models\Trace;
use App\Models\Tracker;
use App\Observers\TraceObserver;
use App\Observers\TrackerObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array<class-string, array<int, class-string>>
	 */
	protected $listen = [
		Registered::class => [
			SendEmailVerificationNotification::class,
		],
		TraceStarted::class => [
			StartTrackerTracking::class,
		],
		TraceStopped::class => [
			StopTrackerTracking::class,
		],
		TrackerStatusChanged::class => [
			ShutdownTracker::class,
		],
	];


	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		Tracker::observe(TrackerObserver::class);
		Trace::observe(TraceObserver::class);
	}
}
