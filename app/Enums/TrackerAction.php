<?php

namespace App\Enums;

enum TrackerAction: string
{
	case Idle = 'idle';
	case Track = 'track';
	case Shutdown = 'shutdown';
}