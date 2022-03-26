<?php

namespace App\Models;

use App\Enums\TrackerStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

/**
 * @property-read int $id
 * @property-read string $uid
 * @property-read int $user_id
 * @property-read string $register_token
 * @property string $name
 * @property ?string $description
 * @property int $update_frequency
 * @property TrackerStatus $status
 * @property bool $registered
 * @property Carbon $last_seen_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 * @property-read ?User $user
 * @property-read Collection<Trace> $paths
 */
class Tracker extends Model implements AuthenticatableContract, MercureSubscriberInterface
{
	use HasFactory, HasApiTokens, Notifiable, Authenticatable;

	protected $fillable = ["name", "description", "update_frequency"];

	protected $casts = [
		"registered" => "bool",
		"status" => TrackerStatus::class,
	];

	protected $dates = ["last_seen_at"];


	/**
	 * @throws Exception
	 */
	public function generateRegisterToken(): string
	{
		if (empty($this->uid)) {
			throw new Exception("Cannot generate a key without tracker's UID.");
		}

		$this->register_token = $token = Str::random(128);
		$this->status = TrackerStatus::Unavailable;
		$this->registered = false;

		return $token;
	}


	public function setAsRegistered()
	{
		$this->register_token = null;
		$this->registered = true;
	}


	public function setupUid()
	{
		$this->uid = Str::uuid()->toString();
	}


	public function generateAccessToken(): NewAccessToken
	{
		$this->tokens()->where("name", "tracker-device")->delete();
		return $this->createToken("tracker-device");
	}


	public function seen(): static
	{
		$this->last_seen_at = Carbon::now();
		return $this;
	}


	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}


	public function traces(): HasMany
	{
		return $this->hasMany(Trace::class);
	}


	public function isBanned(): bool
	{
		return $this->status === TrackerStatus::Banned;
	}


	public function broadcastChannel(): string
	{
		return "App.Models.Tracker.$this->uid";
	}


	public function receivesBroadcastNotificationsOn(): string
	{
		return "App.Models.Tracker.$this->uid";
	}


	public function getMercureType(): string
	{
		return self::class;
	}


	public function getMercureName(): string
	{
		return $this->name;
	}


	public function getMercureId(): int|string
	{
		return $this->uid;
	}

	public function getMercurePayload(): array
	{
		return [
			"type" => $this->getMercureType(),
			"name" => $this->getMercureName(),
			"id" => $this->getMercureId(),
		];
	}


}
