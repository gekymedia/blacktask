<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the TaskPolicy
        return $this->user()->can('update', $this->route('task'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'task_date' => ['sometimes', 'date'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'priority' => ['sometimes', 'integer', 'between:0,2'],
            'reminder_at' => ['sometimes', 'nullable', 'date'],
            'is_done' => ['sometimes', 'boolean'],
            'recurrence' => ['sometimes', 'nullable', 'string', 'in:daily,weekly,monthly,yearly'],
            'recurrence_ends_at' => ['sometimes', 'nullable', 'date', 'after:task_date', 'required_with:recurrence'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Task title cannot exceed 255 characters.',
            'category_id.exists' => 'The selected category does not exist.',
            'priority.between' => 'Priority must be between 0 (low) and 2 (high).',
            'recurrence.in' => 'Recurrence must be one of: daily, weekly, monthly, yearly.',
            'recurrence_ends_at.after' => 'Recurrence end date must be after the task date.',
            'recurrence_ends_at.required_with' => 'Recurrence end date is required when recurrence is set.',
        ];
    }
}

