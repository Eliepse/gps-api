<?php

namespace App\Models;

use App\Enums\TraceStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int $tracker_id
 * @property TraceStatus $status
 * @property Carbon $started_at
 * @property Carbon $finished_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Tracker $tracker
 */
class Trace extends Model
{
	use HasFactory;

	protected $fillable = [];

	protected $casts = [
		"status" => TraceStatus::class,
	];

	protected $dates = ["started_at", "finished_at"];


	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}


	public function tracker(): BelongsTo
	{
		return $this->belongsTo(Tracker::class);
	}


	public function coordinates(): HasMany
	{
		return $this->hasMany(Coordinate::class);
	}


	public function isRecording()
	{
		return $this->status === TraceStatus::Recording;
	}
}
