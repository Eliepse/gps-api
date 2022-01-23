<?php

namespace App\Http\Requests;

use App\Models\Trace;
use App\Models\Tracker;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read array $segment
 * @property-read Trace $trace
 */
class GPSTraceRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return is_a($this->user(), Tracker::class, false);
	}


	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			"segment" => ["required", "array", "min:1"],
			"segment.*.lon" => ["required", "float"],
			"segment.*.lat" => ["required", "float"],
			"segment.*.alt" => ["float"],
			"segment.*.time" => ["required", "int"],
		];
	}
}
