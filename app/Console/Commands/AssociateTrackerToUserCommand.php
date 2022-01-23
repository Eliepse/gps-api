<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use App\Models\User;
use Illuminate\Console\Command;

class AssociateTrackerToUserCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tracker:associate {trackerUid} {userId}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Associate a tracker to a user';


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
	 *
	 * @return int
	 */
	public function handle(): int
	{
		/** @var Tracker $tracker */
		$tracker = Tracker::query()->firstWhere("uid", $this->argument("trackerUid"));

		if (! $tracker) {
			$this->error("Tracker not found.");
			return 1;
		}

		if (! $tracker->registered) {
			$this->error("Tracker not registered.");
			return 1;
		}

		if ($tracker->isBanned()) {
			$this->error("Tracker has been banned.");
			return 1;
		}

		/** @var User $user */
		$user = User::query()->find($this->argument("userId"));

		if (! $user) {
			$this->error("User not found.");
			return 1;
		}

		if (! $tracker->user()->associate($user)->save()) {
			$this->warn("Failed associating the tracker.");
			return 1;
		}
		$this->info("Tracker associated");

		return 0;
	}
}
