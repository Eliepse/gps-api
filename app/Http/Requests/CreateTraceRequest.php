<?php

namespace App\Http\Requests;

use App\Models\Tracker;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $tracker_uid
 */
class CreateTraceRequest extends FormRequest
{
	public ?Tracker $tracker;


	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return is_a($this->user(), User::class);
	}


	protected function passedValidation()
	{
		$this->tracker = $this->user()->trackers()->where("uid", $this->tracker_uid)->first();
	}


	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(): array
	{
		return [
			"tracker_uid" => ["required", "exists:trackers,uid"],
		];
	}
}
