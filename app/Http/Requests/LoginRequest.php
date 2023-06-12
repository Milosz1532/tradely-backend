<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 'email' => 'required|email|exists:users,email',
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => trans('validation.email_required'),
            'email.email' => trans('validation.email_email'),
            'email.unique' => trans('validation.email_unique'),
            'password.required' => trans('validation.password_required'),
            'password.string' => trans('validation.password_string'),
        ];
    }
}
