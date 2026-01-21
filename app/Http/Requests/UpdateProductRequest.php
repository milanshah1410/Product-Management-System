<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');
        
        // Admins can update any product
        if (auth()->user()->hasRole('Admin')) {
            return true;
        }
        
        // Users can only update their own products
        return $product && $product->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'description' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                'max:10000',
            ],
            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'date_available' => [
                'sometimes',
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge([
                'title' => strip_tags($this->title),
            ]);
        }
        
        if ($this->has('price')) {
            $this->merge([
                'price' => (float) $this->price,
            ]);
        }
    }
}
