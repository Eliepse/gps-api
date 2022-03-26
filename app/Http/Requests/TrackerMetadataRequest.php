<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read ?float $precision
 * @property-read ?float[] $coordinates
 * @property-read ?object[] $satellites
 */
class TrackerMetadataRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			"precision" => ["float"],
			"coordinates" => ["array"],
			"satellites" => ["array:visible,active"],
			"satellites.visible" => ["array"],
			"satellites.active" => ["array"],
		];
	}
}
