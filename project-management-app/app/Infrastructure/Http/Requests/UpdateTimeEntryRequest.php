<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'sometimes|string',
            'end_time' => 'sometimes|date',
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.date' => 'End time must be a valid date',
        ];
    }
}
