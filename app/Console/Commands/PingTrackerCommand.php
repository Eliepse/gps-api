<?php

namespace App\Console\Commands;

use App\Events\TraceStarted;
use App\Events\Tracker\TrackerStatusChanged;
use App\Models\Tracker;
use App\Notifications\ControlTracker;
use Illuminate\Console\Command;

class PingTrackerCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tracker:ping';

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
		Tracker::first()->notify(new ControlTracker("start"));
		return 0;
	}
}
