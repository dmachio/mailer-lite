<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class FieldFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function type($type)
    {
        return $this->whereType($type);
    }

    public function title($title)
    {
        return $this->whereRaw("MATCH(title) AGAINST(? IN BOOLEAN MODE)", array("*$title*"));
    }
}
