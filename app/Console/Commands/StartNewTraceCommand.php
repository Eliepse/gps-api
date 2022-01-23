<?php

namespace App\Console\Commands;

use App\Enums\TraceStatus;
use App\Enums\TrackerStatus;
use App\Events\TraceStarted;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class StartNewTraceCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:new-trace';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Start a new trace for a user.';


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Execute the console command.
	 */
	public function handle(): int
	{
		$users = User::query()->get(["id", "name"]);
		$choices = $users->keyBy("id")->pluck("name")->toArray();

		$userName = $this->choice("Select a user", $choices);

		/** @var User $user */
		$user = $users->firstWhere("name", $userName);

		if ($user->traces()->where("status", "!=", TraceStatus::Finished->value)->count() > 0) {
			$this->error("This user already have an trace recording.");
			return 1;
		}

		$trackers = $user->trackers()
			->where("registered", true)
			->whereNotIn("status", [TrackerStatus::Banned->value, TrackerStatus::Unavailable->value])
			->get(["id", "name"]);

		if ($trackers->count() === 0) {
			$this->error("This user does not have any tracker available.");
			return 1;
		}

		$trackerTitle = $this->choice("Which tracker?", $trackers->keyBy("id")->map(fn($t) => "$t->name ($t->id)")->toArray());

		$tracker = $trackers[intval(explode(" (", $trackerTitle)[0])];

		$trace = $user->traces()->make([
			"uid" => Str::uuid()->toString(),
			"status" => TraceStatus::Recording,
			"tracker_id" => $tracker->id,
		]);

		$trace->save();

		TraceStarted::dispatch($trace);

		return 0;
	}
}
