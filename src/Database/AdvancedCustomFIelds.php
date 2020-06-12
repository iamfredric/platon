<?php

namespace Platon\Database;

trait AdvancedCustomFIelds
{
    /**
     * Advanced custom fields getter
     *
     * @param  null $fields
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFieldsAttribute($fields = null)
    {
        if (! $this->attributes->has('fields')) {
            $this->attributes->put('fields', collect(get_fields($this->id)));
        }

        return $this->attributes->get('fields');
    }
}
