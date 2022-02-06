<?php

namespace App\Broadcasting;

use App\Models\Tracker;

class ControlTrackerChannel
{
	/**
	 * Create a new channel instance.
	 *
	 * @return void
	 */
	public function __construct() { }


	/**
	 * Authenticate the user's access to the channel.
	 *
	 * @param Tracker $tracker
	 * @param string $uid
	 *
	 * @return bool|array
	 */
	public function join(Tracker $tracker, string $uid): bool|array
	{
		if ($tracker->uid !== $uid) {
			return false;
		}

		if ($tracker->isBanned()) {
			return false;
		}

		return true;
	}
}
