<?php

namespace App\Http\Controllers\Trackers;

use App\Http\Requests\RegisterTrackerRequest;
use App\Models\Tracker;

class RegisterTrackerController
{
	public function __invoke(RegisterTrackerRequest $request, Tracker $tracker): array
	{
		if ($tracker->isBanned()) {
			abort(403);
		}

		if ($request->token !== $tracker->register_token) {
			abort(401);
		}

		$tracker->setAsRegistered();
		$tracker->seen();
		$tracker->save();

		return [
			"token" => $tracker->generateAccessToken()->plainTextToken,
		];
	}
}