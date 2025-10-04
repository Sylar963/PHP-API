<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id' => 'required|uuid|exists:tasks,id',
            'start_time' => 'required|date',
            'description' => 'sometimes|string',
            'end_time' => 'sometimes|nullable|date|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'task_id.required' => 'Task ID is required',
            'task_id.uuid' => 'Task ID must be a valid UUID',
            'task_id.exists' => 'Task does not exist',
            'start_time.required' => 'Start time is required',
            'start_time.date' => 'Start time must be a valid date',
            'end_time.date' => 'End time must be a valid date',
            'end_time.after' => 'End time must be after start time',
        ];
    }
}
