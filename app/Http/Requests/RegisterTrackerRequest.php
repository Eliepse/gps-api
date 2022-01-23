<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $token
 */
class RegisterTrackerRequest extends FormRequest
{

	/**
	 * Get the validation rules that apply to the request.
	 */
	public function rules(): array
	{
		return [
			"token" => ["required", "string"],
		];
	}
}
