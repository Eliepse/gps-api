<?php

namespace App\Console\Commands;

use App\Models\Tracker;
use Illuminate\Console\Command;
use Transliterator;

class CreateTrackerCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tracker:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new tracker and return both the key and token to register.';
	private Transliterator $transliterator;


	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->transliterator = Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII; :: NFD; :: [:Nonspacing Mark:] Remove; :: Lower(); :: NFC;', Transliterator::FORWARD);
	}


	/**
	 * Execute the console command.
	 */
	public function handle(): int
	{
		$rawName = $this->ask("Name of the tracking device (min: 4 characters)");
		$name = trim($this->transliterator->transliterate($rawName));

		if (empty($name)) {
			$this->error("The name cannot be empty.");
			return 1;
		}

		if (strlen($name) < 4) {
			$this->error("The name must be at least 4 character long.");
			return 1;
		}

		if (Tracker::query()->firstWhere("name", $name)) {
			$this->error("A device already exists with this name.");
			return 1;
		}

		$tracker = new Tracker(["name" => $name]);

		if ($this->confirm("Do you want to write a description?")) {
			$tracker->description = $this->ask("Write a description about this device");
		}

		$tracker->setupUid();
		$token = $tracker->generateRegisterToken();
		$tracker->save();

		$this->comment("UID:");
		$this->info($tracker->uid);
		$this->comment("\n==========================\n");
		$this->comment("Registration token:");
		$this->info($token);

		return 0;
	}
}
