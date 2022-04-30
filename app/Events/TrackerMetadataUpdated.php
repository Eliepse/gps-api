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

	private string $tracker_uid;

	/** @var int[] array */
	public array $coordinate = [];

	public int $activeSatellitesCount = 0;

	public int $visibleSatellitesCount = 0;

	public ?float $precision;


	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(private Tracker $tracker, private array $metadata)
	{
		$lastCoord = array_slice($metadata["coordinates"], -1)[0] ?? [];

		$this->tracker_uid = $this->tracker->uid;

		if (isset($lastCoord["lat"], $lastCoord["lon"])) {
			$this->coordinate = [$lastCoord["lat"], $lastCoord["lon"]];
		}

		if (isset($lastCoord["satellites"])) {
			$this->activeSatellitesCount = count($lastCoord["satellites"]["active"] ?? 0);
			$this->visibleSatellitesCount = count($lastCoord["satellites"]["visible"] ?? 0);
		}

		$this->precision = $lastCoord["precision"] ?? null;
	}


	/**
	 * Get the channels the event should broadcast on.
	 */
	public function broadcastOn(): Channel
	{
		return new Channel($this->tracker->getBroadcastToUserChannel(), true);
	}
}
