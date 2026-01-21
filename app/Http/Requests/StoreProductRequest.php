<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
                'regex:/^\d+(\.\d{1,2})?$/', // Ensure max 2 decimal places
            ],
            'date_available' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The product title is required.',
            'title.min' => 'The product title must be at least 3 characters.',
            'title.max' => 'The product title cannot exceed 255 characters.',
            'description.required' => 'The product description is required.',
            'description.min' => 'The product description must be at least 10 characters.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
            'price.regex' => 'The price format is invalid. Use up to 2 decimal places.',
            'date_available.required' => 'The available date is required.',
            'date_available.after_or_equal' => 'The available date cannot be in the past.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        $this->merge([
            'title' => strip_tags($this->title),
            'price' => $this->price ? (float) $this->price : null,
        ]);
    }
}
