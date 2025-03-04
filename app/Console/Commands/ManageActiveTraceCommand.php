<?php

namespace App\Console\Commands;

use App\Enums\TraceStatus;
use App\Models\Trace;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ManageActiveTraceCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:trace';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Manage user\'s traces (create or stop active one).';


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

		/** @var Trace $activeTrace */
		$activeTrace = $user->traces()->where("status", "!=", TraceStatus::Finished->value)->first();

		if ($activeTrace) {
			$this->info("This user already have an active trace.");

			if ($this->confirm("Do you want to stop it?")) {
				$activeTrace->status = TraceStatus::Finished;
				$activeTrace->save();
				$this->info("Trace stopped.");
			}
			return 0;
		}

		if (! $this->confirm("No active trace, do you want to create one?")) {
			return 0;
		}

		$tracker = $user->tracker;

		if ($tracker->isBanned() || $tracker->registered) {
			$this->error("This user does not have any tracker available.");
			return 1;
		}

		$trace = $user->traces()->make([
			"uid" => Str::uuid()->toString(),
			"status" => TraceStatus::Recording,
			"tracker_id" => $tracker->id,
		]);
		$trace->save();
		$this->info("Trace created and started.");
		return 0;
	}
}
