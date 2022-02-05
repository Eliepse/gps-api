<?php

namespace App\Broadcasting;

use App\Enums\TrackerStatus;
use App\Models\Tracker;
use App\Models\User;

class TrackerChannel
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
	public function join(mixed $authModel, string $uid): bool|array
	{
		/** @var ?Tracker $tracker */
		$tracker = Tracker::query()->where("status", "!=", TrackerStatus::Banned)->firstWhere("uid", $uid);

		if (! $tracker) {
			return false;
		}

		if ($authModel->is($tracker)) {
			$tracker->status = TrackerStatus::Ready;
			$tracker->save();
			return array_merge(["type" => "tracker", $tracker->toArray()]);
		}

		if (is_a($authModel, User::class, false) && $tracker->user_id === $authModel->id) {
			return array_merge(["type" => "user", $authModel->toArray()]);
		}

		return false;
	}
}
