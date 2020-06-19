<?php

namespace Faktory;

class Term
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
     * Save the term into the database and return the instance
     * of the object
     *
     * @return object
     */
    public function save(): object
    {
        $result = wp_insert_term($this->name, $this->taxonomy, $this);

        if (is_array($result)) {
            if (!taxonomy_exists($this->taxonomy)) {
                register_taxonomy($this->taxonomy, null);
            }

            $this->ID = $this->term_id = $result["term_id"];
            $this->term_taxonomy_id = $result["term_taxonomy_id"];
            return $this;
        }

        throw new \Exception("Faktory Term could not be saved to the database.");
    }

}
