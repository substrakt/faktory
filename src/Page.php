<?php

namespace Faktory;

class Page
{

    /**
     * @param mixed $properties An array or object to use as properties for the class
     */
    public function __construct(array|object $properties = [])
    {
        # Loop through the passed properties array|object and set them
        # as properties on the object
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

    /**
     * Return the shorthand key map
     * The map is used to transform the keys from the shorthand to the keys
     * that WordPress expects.
     *
     * @return array
     */
    public static function keys(): array
    {
        return [
            "author"         => "post_author",
            "name"           => "post_name",
            "type"           => "post_type",
            "title"          => "post_title",
            "date"           => "post_date",
            "date_gmt"       => "post_date_gmt",
            "content"        => "post_content",
            "excerpt"        => "post_excerpt",
            "status"         => "post_status",
            "password"       => "post_password",
            "parent"         => "post_parent",
            "modified"       => "post_modified",
            "modified_gmt"   => "post_modified_gmt",
            "meta"           => "meta_input",
        ];
    }

    /**
     * Return the arguments for the wp_insert_* operations
     * A map is used to transform the keys from the shorthand to the keys
     * that WordPress expects.
     *
     * @param array $args
     * @return array
     */
    public static function setKeys(array $args): array
    {
        $map = static::keys();

        foreach ($args as $key => $value) {
            if (isset($map[$key])) {
                $args[$map[$key]] = $value;
                unset($args[$key]);
            }
        }

        return $args;
    }
}
