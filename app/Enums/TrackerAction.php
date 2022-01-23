<?php

namespace App\Enums;

enum TrackerAction: string
{
	case Idle = 'idle';
	case Start = 'start';
	case Stop = "stop";
	case Shutdown = 'shutdown';
}