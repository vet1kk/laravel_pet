<?php

namespace App\Traits;

use App\Models\Builders\UserBuilder;
use Illuminate\Support\Str;

/**
 * Filterable - Implements support for filters in the QueryBuilder.
 */
trait Filterable
{
    /**
     * Apply filters to query builder.
     *
     * @param array $filters
     *
     * @return Filterable|UserBuilder
     */
    public function filter(array $filters): self
    {
        foreach ($filters as $name => $value) {
            $filterName = $this->getFilterName($name);

            if (!is_null($value) && method_exists($this, $filterName)) {
                $this->$filterName($value);
            }
        }

        return $this;
    }

    /**
     * Returns the name of the filter.
     *
     * @param string $field
     *
     * @return string
     */
    protected function getFilterName(string $field): string
    {
        return 'filter' . Str::of($field)->camel()->ucfirst();
    }
}
