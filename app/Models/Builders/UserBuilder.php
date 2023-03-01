<?php

namespace App\Models\Builders;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    use Filterable;

    public function filterEmail(string $email): Builder
    {
        return $this->where('email', $email);
    }
}
