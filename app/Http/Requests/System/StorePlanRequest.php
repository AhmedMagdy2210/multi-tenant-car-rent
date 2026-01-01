<?php

namespace App\Http\Requests\System;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'name' => 'required|string|unique:plans,name',
            'slug' => 'nullable|string|unique:plans,slug',
            'description' => 'nullable',
            'tier' => 'required|in:free,starter,professional,enterprise',
            'price_monthly' => 'required|decimal:0,1000',
            'price_yearly' => 'required|decimal:0,30000',
            'currency' => 'nullable|string',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ];
    }
}
