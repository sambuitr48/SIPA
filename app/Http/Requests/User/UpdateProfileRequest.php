<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
        ];
    }
}
