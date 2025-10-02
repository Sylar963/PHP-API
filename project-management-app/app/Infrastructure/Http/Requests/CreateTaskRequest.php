<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'nullable|in:todo,in_progress,in_review,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'project_id' => 'required|uuid|exists:projects,id',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'due_date' => 'nullable|date',
        ];
    }
}
