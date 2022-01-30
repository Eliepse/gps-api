<?php

namespace App\Notifications\tracker;

use App\Models\Trace;
use Illuminate\Notifications\Notification;

class StartTrackerNotification extends Notification
{
	public string $action = "track";


	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(public Trace $trace)
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
			"traceId" => $this->trace->id,
		];
	}
}
