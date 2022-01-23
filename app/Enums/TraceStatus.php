<?php

namespace App\Enums;

enum TraceStatus: string
{
	case Recording = 'recording';
	case Pause = 'pause';
	case Finished = 'finished';
}