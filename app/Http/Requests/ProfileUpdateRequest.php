<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\_\.]+$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This name is already taken. Please choose a different name.',
                'name.max' => 'Username is too long. Please choose a username with 255 characters or fewer.',
                'name.regex' => 'Username can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
                'email.unique' => 'This email is already registered. Please use a different email address.',
                'name.required' => 'Please enter your username.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.'
        ];
    }
}
