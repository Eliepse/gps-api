<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\Point;

class Coordinate extends Model
{
	use HasFactory, PostgisTrait;

	protected $guarded = [];

	protected array $postgisFields = ["location"];

	protected array $postgisTypes = [
		'location' => [
			'geomtype' => 'geography',
			'srid' => 4326,
		],
	];


	public static function newFromTrackerTraceCoordinates(array $coord)
	{
		["lat" => $lat, "lon" => $lon, "alt" => $alt, "time" => $time] = $coord;

		return new Coordinate([
			"location" => new Point($lat, $lon, $alt),
			"recorded_at" => Carbon::createFromTimestamp($time),
		]);
	}


	public function trace(): BelongsTo
	{
		return $this->belongsTo(Trace::class);
	}
}
