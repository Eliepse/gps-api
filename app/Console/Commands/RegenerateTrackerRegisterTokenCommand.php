<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use Illuminate\Console\Command;

class RegenerateTrackerRegisterTokenCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tracker:reset-registration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Regenerate tracker device regitration key and token.';


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
		$uid = $this->ask("Tracker UID");

		/** @var ?Tracker $tracker */
		$tracker = Tracker::query()->firstWhere("uid", $uid);

		if (! $tracker) {
			$this->error("Could not find a tracker with the given UID.");
			return 1;
		}

		if (! $this->confirm("Do you want to regenerate registration for $tracker->name?")) {
			return 0;
		}


		$handshake = $tracker->generateRegisterToken();
		$tracker->save();

		$this->comment("Key:");
		$this->info($handshake);
		$this->comment("\n==========================\n");
		$this->comment("Token:");
		$this->info($tracker->register_token);

		return 0;
	}
}
