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
        # Register the taxonomy if it does not exist
        if (!taxonomy_exists($this->taxonomy)) {
            register_taxonomy($this->taxonomy, null);
        }

        $term = term_exists($this->name, $this->taxonomy);

        # If the term doesn't exist, save it to the database
        if (is_null($term)) {
            $term = wp_insert_term($this->name, $this->taxonomy, $this);
        }

        # An array returned by either term_exists or wp_insert_term
        # means we have a term in the database
        if (is_array($term)) {
            $this->ID = $this->term_id = $term["term_id"];
            $this->term_taxonomy_id = $term["term_taxonomy_id"];
            return $this;
        }

        throw new \Exception("Faktory Term could not be saved to the database. {$term->get_error_message()}");
    }

}
