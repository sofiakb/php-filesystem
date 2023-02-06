<?php


namespace Sofiakb\Filesystem\Tools;


class Helpers
{

    /**
     * Determine whether the current environment is Windows based.
     *
     * @return bool
     */
    public static function windows_os(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    /**
     * @param null $size
     * @param bool $unit
     * @return string
     */
    public static function humanizeSize($size = null, bool $unit = true): string
    {
        if ($size / 1000 < 1000) {
            $size = $size / 1000;
            $unity = 'Ko';
        } elseif ($size / 1000 / 1000 < 1000) {
            $size = $size / 1000 / 1000;
            $unity = 'Mo';
        } else {
            $size = $size / 1000 / 1000 / 1000;
            $unity = 'Go';
        }
        return number_format($size, 2, '.', ' ') . ($unit ? " $unity" : '');
    }

}