<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateUserCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new user';


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
		$user = new User();

		do {
			$user->name = $this->ask("User name?");
		} while (Validator::make(["v" => $user->name], ["v" => "required|string|between:4,32|unique:users,name"])->fails());

		do {
			$user->email = $this->ask("User email?");
		} while (Validator::make(["v" => $user->email], ["v" => "required|email|unique:users,email"])->fails());

		do {
			$user->password = $this->secret("User password?");
		} while (Validator::make(["v" => $user->password], ["v" => "required|string|between:12,64"])->fails());

		$user->save();

		$this->info("User id: $user->id");

		return 0;
	}
}
