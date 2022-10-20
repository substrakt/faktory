<?php

namespace Faktory;

class Faktory
{
    /**
     * An array of directories to load factories from.
     */
    public static array $dirs = [];

    /**
     * Add the directory array of directories to search for factories.
     *
     * @param string|array the directories to add
     * @return array The directories
     */
    public static function addDirs(string|array $dirs): array
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
    public static function create(string $type = "page", array $args = []): object
    {
        $f = static::generate($type, $args);
        $f->save();
        return $f;
    }

    /**
     * Returns are new Faktory object.
     * The object is not saved to the database.
     *
     * @param string $type The name of the factory to load.
     * @param array $args Array of properties and values to override.
     * @return object
     */
    public static function new(string $type = "page", array $args = []): object
    {
        return static::generate($type, $args);
    }

    /**
     * Load a factory from the available directories and return the containing array.
     *
     * @param string $file name of factory to load.
     * @paran array $args the arguments to use as overwites to the factory
     * @return array
     */
    protected static function loadFactory(string $file, array $args = []): array
    {
        # $default = Was the default factory loaded
        list($pathToFactory, $default) = static::locateFactory($file);

        # If the default factory was loaded set the post type to match the name
        # of the file var. This allows for factories to be created for post types
        # that don't have a corresponding factory file.
        if ($default && !isset($args["post_type"])) {
            $args["post_type"] = $file;
        }

        $factory = require $pathToFactory;

        # Obtain the class and call the setKeys method to update any shorthand
        # properties to the full properties WordPress expects
        $class = $factory["class"];
        $data = array_merge($factory, $class::setKeys($args));

        # Merge top level array to allow for default array data to be stored
        # in the factory file.
        foreach ($factory as $k => $v) {
            if (is_array($v) && isset($args[$k])) {
                $data[$k] = array_merge($factory[$k], $args[$k]);
            }
        }

        return $data;
    }

    /**
     * Return an array containing the path the factory to be used and a boolean
     * as to whether it is the default factory or not.
     *
     * @param string $fileName The name of the factory file searched for
     * @return array [string, boolean]
     */
    protected static function locateFactory(string $fileName): array
    {
        # Parent directory
        $dir = dirname(__DIR__);
        # Add the directory to the list of directories that contain factories
        static::addDirs(["{$dir}/factories"]);

        # Loop through all of the directories to find the factor in
        # all the registered factory directories
        foreach (static::$dirs as $path) {
            $path = rtrim($path, "/");

            if (file_exists($fullPath = "{$path}/{$fileName}.php")) {
                return [$fullPath, false];
            }
        }

        # Return the default factory
        return ["{$dir}/factories/page.php", true];
    }

    /**
     * Loads the factory data and return the factory as a class.
     *
     * @param string $type the factory to load
     * @param array $args the arguments passed from the test to overwite the defaults
     * @param bool $save should the data be saved to the database
     * @return object
     */
    protected static function generate(string $type, array $args): object
    {
        $args = array_merge(
            ["class" => Page::class],
            static::loadFactory($type, $args)
        );

        $class = $args["class"];
        unset($args["class"]);

        return new $class($args);
    }

}
