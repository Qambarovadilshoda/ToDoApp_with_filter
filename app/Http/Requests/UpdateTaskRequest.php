<?php

namespace App\Http\Requests;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|min:5',
            'description' => 'nullable|max:225',
            'term' => 'nullable|date_format:Y-m-d',
            'time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $today = Carbon::now('Asia/Tashkent')->toDateString();
                    $currentTime = Carbon::now('Asia/Tashkent')->format('H:i');

                    if (request('term') === $today && $value <= $currentTime) {
                        $fail('The ' . $attribute . ' must be a time after the current time for today.');
                    }
                }
            ],
        ];
    }
}
