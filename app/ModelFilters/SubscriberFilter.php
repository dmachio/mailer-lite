<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class SubscriberFilter extends ModelFilter
{
    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function state($state)
    {
        return $this->whereState($state);
    }

    public function search($search)
    {
        return $this->whereRaw("MATCH(name,email) AGAINST(? IN BOOLEAN MODE)", array("*$search*"));
    }

    public function name($name)
    {
        return $this->whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", array("*$name*"));
    }

    public function email($email)
    {
        return $this->whereRaw("MATCH(email) AGAINST(? IN BOOLEAN MODE)", array("*$email*"));
    }
}
