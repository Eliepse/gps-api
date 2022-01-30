<?php

namespace App\Console\Commands;

use App\Enums\TrackerStatus;
use App\Models\Tracker;
use Illuminate\Console\Command;

class ChangeTrackerStatusCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tracker:change {uid}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';


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
	public function handle()
	{
		/** @var Tracker $tracker */
		$tracker = Tracker::query()->firstWhere("uid", $this->argument("uid"));

		$this->info("Current status of '$tracker->name': {$tracker->status->value}");

		$action = $this->choice("What should be the new status?", array_map(fn($a) => $a->value, TrackerStatus::cases()));

		$tracker->status = TrackerStatus::from($action);
		$tracker->save();

		return 0;
	}
}
