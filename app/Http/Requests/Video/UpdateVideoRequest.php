<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
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
            'course_id' => 'sometimes|exists:courses,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'url' => 'sometimes|string|url',
            'duration' => 'sometimes|nullable|integer',
            'order' => 'sometimes|nullable|integer',
        ];
    }
}