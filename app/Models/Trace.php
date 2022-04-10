<?php

namespace App\Models;

use App\Enums\TraceStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read int $id
 * @property string $uid
 * @property int $user_id
 * @property int $tracker_id
 * @property TraceStatus $status
 * @property float $length
 * @property Carbon $started_at
 * @property Carbon $finished_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Tracker $tracker
 * @property-read Collection<Coordinate> $coordinates
 */
class Trace extends Model
{
	use HasFactory;

	protected $fillable = ["uid", "status", "length", "tracker_id", "started_at"];

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


	public function isRecording(): bool
	{
		return $this->status === TraceStatus::Recording;
	}
}
