<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
     /**
      * Get the validation rules that apply to the request.
      *
      * @return array
      *
      * @OA\Schema(
      *     schema="UpdateUserRequest",
      *     type="object",
      *     title="Update User Request",
      *     @OA\Property(property="name", type="string", example="John Doe"),
      *     @OA\Property(property="email", type="string", example="example@gmail.com"),
      *     @OA\Property(property="password", type="string", example="password", minLength=8),
      *     @OA\Property(property="password_confirmation", type="string", example="password")
      * )
      */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed'
        ];
    }
}
