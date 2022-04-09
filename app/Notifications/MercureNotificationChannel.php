<?php

namespace App\Notifications;

use Duijker\LaravelMercureBroadcaster\Broadcasting\Channel;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Contracts\Broadcasting\HasBroadcastChannel;
use Illuminate\Notifications\Notification;

class MercureNotificationChannel
{
	public function __construct(private BroadcastManager $broadcast) { }


	/**
	 * @param HasBroadcastChannel $notifiable
	 * @param Notification $notification
	 *
	 * @return void
	 */
	public function send(HasBroadcastChannel $notifiable, Notification $notification)
	{
		$channel = new Channel($notifiable->broadcastChannel(), true);
		$this->broadcast->broadcast([$channel], get_class($notification), $notification->toArray($notifiable) ?? []);
	}
}