<?php

class Faktory
{
    public static function create($type = "post", $args = []): object
    {
        return static::generate($type, $args, $save = true);
    }

    /**
     * Returns are new object Faktory.
     * @param array $args Array of properties and values to override.
     * @return object
     */
    public static function new($type = "post", $args = []): object
    {
        return static::generate($type, $args, $save = false);
    }

    /**
     * Load a Faktory from the faktories directorty and return the containing array.
     * @param string $file name of faktory to load.
     * @return array
     */
    protected static function loadFactory(string $file, array $args = []): array
    {
        $defaults = [];
        $rootPath = realpath(dirname(dirname(__FILE__)));

        if (file_exists($file = "{$rootPath}/factories/{$file}.php")) {
            $defaults = require $file;
        }

        return array_merge($defaults, $args);
    }

    protected static function generate($type, $args, $save)
    {
        $class = "FaktoryPost";
        $args  = static::loadFactory($type, $args);

        if (isset($args["class"])) {
            $class = $args["class"];
            unset($args["class"]);
        }

        if ($save && function_exists("\wp_insert_post")) {
            $args["ID"] = wp_insert_post($args);
        }

        return new $class($args);
    }
}
