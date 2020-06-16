<?php

namespace Faktory;

class Post
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
        $result = wp_insert_post($this);

        if (is_integer($result)) {
            $this->ID = $result;
            return $this;

        }

        throw new \Exception("Faktory Post could not be saved to the database.");
    }

}
