<?php
namespace Jobs;

abstract class AbstractJob
{
    public static function instance()
    {
        $class = get_called_class();
        return new $class();
    }

    abstract function init($data);

    abstract function work();
}