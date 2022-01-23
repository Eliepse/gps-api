<?php

namespace App\Events\Tracker;

use App\Models\Tracker;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackerStatusChanged implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public Tracker $tracker) { }


	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel("App.Models.Tracker.{$this->tracker->uid}");
	}


	public function broadcastWith(): array
	{
		return [
			"uid" => $this->tracker->uid,
			"state" => $this->tracker->status,
		];
	}
}
