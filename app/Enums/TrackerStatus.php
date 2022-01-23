<?php

namespace App\Enums;

enum TrackerStatus: string
{
	case Unavailable = 'unavailable';
	case Ready = 'ready';
	case Tracking = "tracking";
	case Banned = 'banned';
}