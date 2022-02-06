<?php

use App\Broadcasting\ControlTrackerChannel;
use App\Broadcasting\TrackingChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel("tracking.{user}", TrackingChannel::class, ["middleware" => ["web", "api"]]);
Broadcast::channel("App.Models.Tracker.{uid}", ControlTrackerChannel::class, ["middleware" => ["api", "tracker"]]);
