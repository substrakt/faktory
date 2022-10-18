<?php

namespace Faktory;

class Term extends Page
{
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

    /**
     * Return the shorthand key map
     * The map is used to transform the keys from the shorthand to the keys
     * that WordPress expects.
     *
     * @return array
     */
    public static function keys(): array
    {
        return [];
    }
}
