<?php

use App\Models\Trace;
use Illuminate\Database\Grammar;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoordinatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::createExtensionIfNotExists('postgis');
		Schema::create('coordinates', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Trace::class);
			$table->point("location");
			$table->float("precision")->nullable();
			$table->timestamp("recorded_at");

			$table->unique(["trace_id", "location", "recorded_at"]);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('coordinates');
		Schema::dropExtensionIfExists('postgis');
	}
}
