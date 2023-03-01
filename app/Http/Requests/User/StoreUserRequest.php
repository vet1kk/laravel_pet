<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     * @OA\Schema(
     *     schema="StoreUserRequest",
     *     type="object",
     *     title="Store User Request",
     *     @OA\Property(property="name", required={"true"}, type="string", example="John Doe"),
     *     @OA\Property(property="email", required={"true"}, type="string", example="example@gmail.com"),
     *     @OA\Property(property="password", required={"true"}, type="string", example="password", minLength=8),
     *     @OA\Property(property="password_confirmation", required={"true"}, type="string", example="password")
     * )
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
