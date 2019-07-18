<?php

class FaktoryPost
{
    public function __construct($params = [])
    {
        foreach ($params as $k => $v) {
            $this->$k = $v;
        }
    }

    public function as($class)
    {
        return new $class($this);
    }

    public function save()
    {
        if (function_exists("\wp_insert_post")) {
            $this->ID = \wp_insert_post($this);
        }

        return $this;
    }

}
