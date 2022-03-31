<?php

namespace App\Events;

use App\Models\Coordinate;
use App\Models\Trace;
use Duijker\LaravelMercureBroadcaster\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TraceCoordinatesUpdated implements ShouldBroadcast
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public Collection $coordinates;


	/**
	 * Create a new event instance.
	 *
	 * @param Trace $trace
	 * @param Collection<Coordinate> $coordinates
	 */
	public function __construct(private Trace $trace, Collection $coordinates)
	{
		$this->coordinates = $coordinates->map(fn(Coordinate $coordinate) => $coordinate->getLatLng());
	}


	/**
	 * Get the channels the event should broadcast on.
	 */
	public function broadcastOn(): Channel
	{
		return new Channel("/user/{$this->trace->user_id}/trace/{$this->trace->uid}", true);
	}
}
