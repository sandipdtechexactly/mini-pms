<?php

namespace App\Http\Requests\Api\Task;

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
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'sometimes|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'sometimes|in:pending,in_progress,in_review,completed,blocked',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|integer|min:0',
        ];
    }
}
