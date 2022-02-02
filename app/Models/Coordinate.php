<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\Point;

/**
 * @property-read int $id
 * @property Point $location
 * @property float $precision
 * @property int $trace_id
 * @property Trace $trace
 * @property int $recorded_at
 */
class Coordinate extends Model
{
	use HasFactory, PostgisTrait;

	protected $guarded = [];

	protected array $postgisFields = ["location"];


	public static function newFromTrackerTraceCoordinates(array $coord): Coordinate
	{
		return new Coordinate([
			"location" => new Point($coord["lat"], $coord["lon"], $coord['alt'] ?? null),
			"recorded_at" => Carbon::createFromTimestamp($coord["time"]),
		]);
	}


	public function trace(): BelongsTo
	{
		return $this->belongsTo(Trace::class);
	}


	public function toInsertQueryArray()
	{
		return array_merge(
			$this->toArray(),
			["location" => $this->location->toWKT()],
		);
	}
}
