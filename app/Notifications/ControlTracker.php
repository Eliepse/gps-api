<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ControlTracker extends Notification
{

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(public string $action)
	{
	}


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
