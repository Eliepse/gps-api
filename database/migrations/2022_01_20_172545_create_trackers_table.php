<?php

use App\Enums\TrackerStatus;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trackers', function (Blueprint $table) {
			$table->id();
			$table->uuid("uid");
			$table->string("register_token")
				->nullable()
				->comment("This token should be sent by the device, to register it. Then it should be deleted from the database.");
			$table->foreignIdFor(User::class)->nullable();
			$table->string("name")->unique();
			$table->string("description")->nullable();
			$table->unsignedInteger("update_frequency")
				->default(2000)
				->comment("The frequency at which the device should send updates the gps location.");
			$table->string("status")->default(TrackerStatus::Unavailable->value);
			$table->boolean("registered")->default(false);
			$table->timestamp("last_seen_at")->nullable();
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
		Schema::dropIfExists('trackers');
	}
}
