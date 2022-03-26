<?php

namespace App\Events;

use App\Models\Tracker;
use Duijker\LaravelMercureBroadcaster\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackerMetadataUpdated implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public Tracker $tracker, public array $metadata)
	{
		//
	}


	/**
	 * Get the channels the event should broadcast on.
	 */
	public function broadcastOn(): Channel
	{
		return new Channel($this->tracker->getBroadcastToUserChannel(), true);
	}
}
