<?php

namespace App\Http\Requests\Api;

use App\Traits\ValidationErrorsTrait;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
        $this->checkUnknownKey($this->keys(), ['email', 'password']);

        return [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
    }
}
