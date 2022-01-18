<?php

namespace Platon\Database;

/**
 * @property \Platon\Components\Components $components
 */
trait Components
{
    /**
     * @param $components
     *
     * @return \Platon\Components\Components
     */
    public function getComponentsAttribute($components)
    {
        return $this->components('components');
    }

    public function components($fieldname = 'components', $prefix = null)
    {
        $key = $prefix ? "{$prefix}-components" : 'components';

        if (! $this->attributes->has($key)) {
            $this->attributes->put($key, new \Platon\Components\Components($this->fields->get($fieldname), $prefix));
        }

        return $this->attributes->get($key);
    }
}
