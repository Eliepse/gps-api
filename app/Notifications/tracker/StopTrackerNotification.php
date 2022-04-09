<?php

namespace App\Notifications\tracker;

use App\Models\MercureSubscriberInterface;
use App\Notifications\MercureNotificationChannel;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class StopTrackerNotification extends Notification
{
	public string $action = "idle";


	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct() { }


	/**
	 * Get the notification's delivery channels.
	 *
	 * @param MercureSubscriberInterface|Notifiable $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable)
	{
		return [MercureNotificationChannel::class];
	}


	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 *
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			'action' => $this->action,
		];
	}
}
