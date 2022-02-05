<?php

namespace App\Broadcasting;

use App\Models\Tracker;
use App\Models\User;

class TrackingChannel
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
	 * @param Tracker|User $authModel
	 * @param string $uid
	 *
	 * @return bool|array
	 */
	public function join(mixed $authModel, User $user): bool|array
	{
		if ($authModel->is($user)) {
			return array_merge(["type" => "user"], $authModel->toArray());
		}

		if (is_a($authModel, Tracker::class) && $authModel->user_id === $user->id) {
			return array_merge(["type" => "tracker"], $authModel->toArray());
		}

		return false;
	}
}
