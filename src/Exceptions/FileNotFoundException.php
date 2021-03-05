<?php


namespace Ssf\Filesystem\Exceptions;


use Exception;
use Throwable;

class FileNotFoundException extends Exception
{
    public function __construct(string $path = "", $code = 0, Throwable $previous = null)
    {
        $message = "File does not exist at path {$path}.";
        parent::__construct($message, $code, $previous);
    }
}