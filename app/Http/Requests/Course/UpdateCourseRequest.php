<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
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
            'subcategory_id' => 'sometimes|exists:subcategories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'sometimes|nullable|string',
            'objectives' => 'sometimes|nullable|string',
            'thumbnail' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'level' => 'sometimes|in:beginner,intermediate,advanced',
            'is_published' => 'sometimes|boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
        ];
    }
}