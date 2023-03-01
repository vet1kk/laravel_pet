<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     *
     * @OA\Schema(
     *     schema="LoginRequest",
     *     required={"email", "password"},
     *     type="object",
     *     title="Login Request",
     *     @OA\Property(property="email", type="string", format="email", example="example@gmail.com"),
     *     @OA\Property(property="password", type="string", format="password", example="password"),
     * )
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }
}
