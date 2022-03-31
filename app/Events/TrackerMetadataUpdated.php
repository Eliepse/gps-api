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

	/** @var int[] array */
	public array $coordinate;
	public int $activeSatellitesCount;
	public int $visibleSatellitesCount;


	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(public Tracker $tracker, private array $metadata)
	{
		$last = array_slice($metadata["coordinates"], -1)[0];

		if (isset($last["lat"], $last["lon"])) {
			$this->coordinate = [$last["lat"], $last["lon"]];
		}

		if (isset($last["satellites"])) {
			$this->activeSatellitesCount = count($last["satellites"]["active"] ?? 0);
			$this->visibleSatellitesCount = count($last["satellites"]["visible"] ?? 0);
		}
	}


	/**
	 * Get the channels the event should broadcast on.
	 */
	public function broadcastOn(): Channel
	{
		return new Channel($this->tracker->getBroadcastToUserChannel(), true);
	}
}
