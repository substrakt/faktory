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

}
