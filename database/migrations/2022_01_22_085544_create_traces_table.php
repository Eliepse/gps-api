<?php

use App\Models\Tracker;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('traces', function (Blueprint $table) {
			$table->id();
			$table->uuid("uid");
			$table->foreignIdFor(User::class);
			$table->foreignIdFor(Tracker::class);
			$table->enum("status", ["recording", "pause", "finished"])->default("recording");
			$table->timestamp("started_at")->nullable();
			$table->timestamp("finished_at")->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('traces');
	}
}
