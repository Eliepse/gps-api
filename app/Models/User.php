<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property Carbon $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<Tracker> $trackers
 * @property Collection<Trace> $traces
 */
class User extends Authenticatable implements MercureSubscriberInterface
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];


	public function trackers(): HasMany
	{
		return $this->hasMany(Tracker::class);
	}


	public function traces(): HasMany
	{
		return $this->hasMany(Trace::class);
	}


	public function getMercureName(): string
	{
		return $this->name;
	}


	public function getMercureType(): string
	{
		return self::class;
	}


	public function getMercureId(): int|string
	{
		return $this->id;
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
