<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class SignupRequest extends FormRequest
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
            'login' => 'required|string|unique:users,login|max:55',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
            ]
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'login.required' => trans('validation.login_required'),
            'login.string' => trans('validation.login_string'),
            'login.max' => trans('validation.login_max'),
            'login.unique' => trans('validation.login_unique'),

            'email.required' => trans('validation.email_required'),
            'email.email' => trans('validation.email_email'),
            'email.unique' => trans('validation.email_unique'),
            'password.required' => trans('validation.password_required'),
            'password.string' => trans('validation.password_string'),
        ];
    }
}
