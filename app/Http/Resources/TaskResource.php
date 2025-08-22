<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'estimated_hours' => $this->estimated_hours,
            'project_id' => $this->project_id,
            'assigned_to' => $this->whenLoaded('assignee', function () {
                return new UserResource($this->assignee);
            }),
            'created_by' => $this->whenLoaded('creator', function () {
                return new UserResource($this->creator);
            }),
            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project->id,
                    'name' => $this->project->name,
                    'code' => $this->project->code,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
        ];
    }
}
