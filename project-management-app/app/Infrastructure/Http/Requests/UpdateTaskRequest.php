<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|string|in:todo,in_progress,in_review,done,cancelled',
            'priority' => 'sometimes|string|in:low,medium,high,urgent',
            'due_date' => 'sometimes|nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Task title must not exceed 255 characters',
            'status.in' => 'Status must be one of: todo, in_progress, in_review, done, cancelled',
            'priority.in' => 'Priority must be one of: low, medium, high, urgent',
            'due_date.date' => 'Due date must be a valid date',
        ];
    }
}
