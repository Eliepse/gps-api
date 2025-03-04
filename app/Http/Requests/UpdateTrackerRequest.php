<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read ?float $precision
 * @property-read ?object[] $coordinates
 * @property-read ?object[] $satellites
 */
class UpdateTrackerRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		return [
			"coordinates" => ["array"],
			"coordinates.*.lon" => ["required", "numeric"],
			"coordinates.*.lat" => ["required", "numeric"],
			"coordinates.*.alt" => ["float"],
			"coordinates.*.time" => ["required", "numeric"],
			"coordinates.*.precision" => ["numeric", "nullable"],
			"satellites" => ["array:visible,active"],
			"satellites.visible" => ["integer"],
			"satellites.active" => ["integer"],
		];
	}
}
