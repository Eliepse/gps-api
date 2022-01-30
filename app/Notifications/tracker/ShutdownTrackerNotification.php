<?php

namespace App\Notifications\tracker;

use Illuminate\Notifications\Notification;

class ShutdownTrackerNotification extends Notification
{
	public string $action = "shutdown";

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct() {}


	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['broadcast'];
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
