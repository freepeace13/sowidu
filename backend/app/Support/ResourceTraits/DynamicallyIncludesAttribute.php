<?php

namespace App\Support\ResourceTraits;

trait DynamicallyIncludesAttribute
{
    /**
     * @var array
     */
    protected $included = ['base'];

    /**
     * Dynamically cache the included attributes
     *
     * @param  array  $attributes
     * @return $this
     */
    public function includeAttributes(...$attributes)
    {
        foreach ($attributes as $attribute) {
            if (!$this->includes($attribute)) {
                $this->included[] = $attribute;
            }
        }

        return $this;
    }

    /**
     * Dynamically remove included attributes
     *
     * @param  array  $attributes
     * @return $this
     */
    public function excludeAttributes(...$attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->includes($attribute)) {
                $key = array_search($attribute, $this->included);
                unset($this->included[$key]);
            }
        }

        return $this;
    }

    /**
     * Determine the given attribute is included
     *
     * @return bool
     */
    public function includes(string $attribute)
    {
        return in_array($attribute, $this->included);
    }
}
