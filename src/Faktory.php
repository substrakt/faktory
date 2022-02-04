<?php

namespace Faktory;

class Faktory
{
    /**
     * An array of directories to load factories from.
     * @var array
     */
    public static $dirs = [];

    /**
     * Add the directory array of directories to search for factories.
     *
     * @param mixed string|array the directories to add
     * @return array The directories
     */
    public static function addDirs($dirs): array
    {
        if (!is_array($dirs)) {
            $dirs = [$dirs];
        }

        static::$dirs = array_unique(array_merge(static::$dirs, $dirs));

        return static::$dirs;
    }

    /**
     * Returns are new Faktory object.
     * The object is saved to the database.
     *
     * @param string $type The name of the factory to load.
     * @param array $args Array of properties and values to override.
     * @return object
     */
    public static function create($type = "post", $args = []): object
    {
        return static::generate($type, $args, $save = true);
    }

    /**
     * Returns are new Faktory object.
     * The object is not saved to the database.
     *
     * @param string $type The name of the factory to load.
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
        # Parent directory
        $dir = dirname(__DIR__);

        # Add the parent directory to the list of directories
        # where a factories folder can be found.
        static::addDirs($dir);

        # Loop through all of the directories to find the factor in
        # all the registered factory directories
        foreach (static::$dirs as $path) {
            $path = rtrim($path, "/");

            if (file_exists($fullPath = "{$path}/factories/{$file}.php")) {
                # Require the factory
                $defaults = require $fullPath;
                # Merge the data in the factory with the data passed in via the test
                $data = array_merge($defaults, $args);
                # Loop through all the data in the factory, for all values that are arrays
                # merge them with the data passed via the test
                foreach ($defaults as $k => $v) {
                    if (is_array($v) and isset($args[$k])) {
                        $data[$k] = array_merge($defaults[$k], $args[$k]);
                    }
                }

                return $data;
            }
        }

        # A factory can't be found
        # Return a post factory but change the post_type to
        # be that of the missing factory.
        # This allows factories for custom post types to be easily created used.
        return array_merge(require "{$dir}/factories/page.php", ['post_type' => $file], $args);
    }

    /**
     * Loads the factory data and return the factory as a class.
     *
     * @param string $type the factory to load
     * @param array $args the arguments passed from the test to overwite the defaults
     * @param bool $save should the data be saved to the database
     * @param object
     */
    protected static function generate(string $type, array $args, bool $save): object
    {
        $args = static::loadFactory($type, $args);

        # Use the class set via the args as the return class
        if (isset($args["class"])) {
            $class = $args["class"];
            unset($args["class"]);
            $factory =  new $class($args);
        } else {
            $factory = new Post($args);
        }

        # Save the post to the database if desired
        if ($save) {
            $factory->save();
        }

        return $factory;
    }
}
