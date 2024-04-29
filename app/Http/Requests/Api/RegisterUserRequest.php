<?php

namespace App\Http\Requests\Api;

use App\Traits\ValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    use ValidationErrorsTrait;
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
        $this->checkUnknownKey($this->keys(), ['name', 'email', 'password', 'password_confirmation']);

        return [
            'name' => 'required|string|between:2,50',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ];
    }
}
