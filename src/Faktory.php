<?php

class Faktory
{
    /**
     * Returns are new object Faktory.
     * @param array $params Array of properties and values to override.
     * @return object
     */
    public static function new($type = "post", $params = []): object
    {
        $params = array_merge(
            static::loadFactory($type), $params
        );

        if (isset($params["class"])) {
            $class = $params["class"];
            unset($params["class"]);
            return new $class($params);
        }

        return new FaktoryPost($params);
    }

    /**
     * Load a Faktory from the faktories directorty and return the containing array.
     * @param string $file name of faktory to load.
     * @return array
     */
    protected static function loadFactory(string $file): array
    {
        $root = realpath(dirname(dirname(__FILE__)));

        if (file_exists($file = "{$root}/factories/{$file}.php")) {
            return require $file;
        }

        return [];
    }
}
