<?php

namespace App\Events;

use App\Enums\TrackerStatus;
use App\Models\Tracker;
use Duijker\LaravelMercureBroadcaster\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackerStatusChanged implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	private TrackerStatus $status;


	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public Tracker $tracker)
	{
		$this->status = $this->tracker->status;
	}


	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel
	 */
	public function broadcastOn()
	{
		return new Channel("App.Models.Tracker.{$this->tracker->uid}");
	}
}
