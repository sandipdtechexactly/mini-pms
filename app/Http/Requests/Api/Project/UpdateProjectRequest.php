<?php

namespace App\Http\Requests\Api\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'manager']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:projects,code,' . $this->project->id,
            'description' => 'nullable|string',
            'status' => 'sometimes|in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'budget' => 'nullable|numeric|min:0',
            'team_members' => 'sometimes|array',
            'team_members.*' => 'exists:users,id',
        ];
    }
}
