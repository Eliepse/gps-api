<?php

namespace App\Broadcasting;

use App\Models\Tracker;

class TrackerControlChannel
{
	/**
	 * Create a new channel instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}


	/**
	 * Authenticate the user's access to the channel.
	 *
	 * @param Tracker $tracker
	 * @param string $uid
	 *
	 * @return bool
	 */
	public function join(Tracker $tracker, $uid)
	{
		return $tracker->uid === $uid;
	}
}
