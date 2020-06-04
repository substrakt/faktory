<?php

class FaktoryPost
{

    /**
     * @param mixed $properties An array or object to use as properties for the class
     */
    public function __construct($properties = [])
    {
        # Loop through the passed properties array|object and set them
        # as propteries on the object
        foreach ((object) $properties as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Return the object as a different class
     *
     * @return object
     */
    public function as($class): object
    {
        return new $class($this);
    }

    /**
     * Save the post into the database and return the instance
     * of the object
     *
     * @return object
     */
    public function save(): object
    {
        if (function_exists("\wp_insert_post")) {
            $this->ID = \wp_insert_post($this);
        }

        return $this;
    }

}
