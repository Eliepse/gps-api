<?php

namespace App\Actions;

use App\Models\Trace;
use Illuminate\Support\Facades\DB;

class UpdateTraceLength
{
	public function __invoke(Trace $trace): bool
	{
		return $this->byId($trace->id);
	}


	public function byId(int $id): bool
	{
		return DB::update(
				"UPDATE traces SET length = (
    		SELECT ST_Length(ST_MakeLine(c.location::geometry ORDER BY recorded_at):: geography)
				FROM coordinates as c
				WHERE c.trace_id = ?
				GROUP BY c.trace_id
			) WHERE id = ?",
				[$id, $id]
			) > 0;
	}
}