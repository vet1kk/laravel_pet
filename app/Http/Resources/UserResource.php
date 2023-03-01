<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     *
     * @OA\Schema(
     *     schema="UserResource",
     *     type="object",
     *     title="User Resource",
     *     @OA\Property(property="id", type="integer", example="1"),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", example="example@gmail.com"),
     *     @OA\Property(property="created_at", type="string", example="2021-01-01 00:00:00"),
     *     @OA\Property(property="updated_at", type="string", example="2021-01-01 00:00:00")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
