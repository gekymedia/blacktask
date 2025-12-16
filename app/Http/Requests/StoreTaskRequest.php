<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'task_date' => ['nullable', 'date', 'after_or_equal:today'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['nullable', 'integer', 'between:0,2'],
            'reminder_at' => ['nullable', 'date', 'after:now'],
            'recurrence' => ['nullable', 'string', 'in:daily,weekly,monthly,yearly'],
            'recurrence_ends_at' => ['nullable', 'date', 'after:task_date', 'required_with:recurrence'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a task title.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'task_date.after_or_equal' => 'Task date cannot be in the past.',
            'category_id.exists' => 'The selected category does not exist.',
            'priority.between' => 'Priority must be between 0 (low) and 2 (high).',
            'reminder_at.after' => 'Reminder must be set in the future.',
            'recurrence.in' => 'Recurrence must be one of: daily, weekly, monthly, yearly.',
            'recurrence_ends_at.after' => 'Recurrence end date must be after the task date.',
            'recurrence_ends_at.required_with' => 'Recurrence end date is required when recurrence is set.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default task_date to today if not provided
        if (!$this->has('task_date')) {
            $this->merge([
                'task_date' => today()->toDateString(),
            ]);
        }

        // Set default priority if not provided
        if (!$this->has('priority')) {
            $this->merge([
                'priority' => 1, // Medium priority by default
            ]);
        }
    }
}

