<?php


namespace Sofiakb\Filesystem\Facades;


use Exception;
use Sofiakb\Filesystem\Filesystem;

class Facade
{

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(Filesystem::class, $name)) {
            return call_user_func_array(array(self::filesystem(), $name), $arguments);
        } else throw new Exception("Method [$name] not found in " . Filesystem::class);
    }

    /**
     * Get the filesystem singleton
     *
     * @return Filesystem|null
     */
    private static function filesystem()
    {
        return Filesystem::getInstance();
    }

}