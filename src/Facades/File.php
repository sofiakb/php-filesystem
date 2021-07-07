<?php


namespace Sofiakb\Filesystem\Facades;

use Sofiakb\Filesystem\Filesystem;

/**
 * Class File
 * @package Sofiakb\Filesystem\Facades
 *
 * @method static bool exists(string $path)
 * @method static bool missing(string $path)
 * @method static string get(string $path, bool $lock = false)
 * @method static int lines(string $file)
 * @method static string hash(string $path)
 * @method static int put(string $path, string $contents, bool $lock = false)
 * @method static void replace(string $path, string $content)
 * @method static int prepend(string $path, string $data)
 * @method static int append(string $path, string $data)
 * @method static mixed chmod(string $path, $mode = null)
 * @method static bool delete($paths)
 * @method static bool move(string $path, string $target)
 * @method static bool rename(string $path, string $target)
 * @method static bool copy(string $path, string $target)
 * @method static void|bool link(string $target, string $link)
 * @method static string name(string $path)
 * @method static string basename(string $path)
 * @method static string dirname(string $path)
 * @method static string extension(string $path)
 * @method static string type(string $path)
 * @method static string mimeType(string $path)
 * @method static int size(string $path, bool $humanize = false)
 * @method static int lastModified(string $path)
 * @method static bool isDirectory(string $directory)
 * @method static bool isReadable(string $directory)
 * @method static bool isWritable(string $directory)
 * @method static bool isFile(string $file)
 * @method static bool makeDirectory(string $path, $mode = 0755, $recursive = false, $force = false)
 * @method static bool moveDirectory(string $from, string $to, $overwrite = false)
 * @method static bool copyDirectory(string $directory, string $destination, $options = null)
 * @method static bool deleteDirectory(string $directory, $preserve = false)
 * @method static bool deleteDirectories(string $directory)
 * @method static bool cleanDirectory(string $directory)
 * @method static \Sofiakb\Filesystem\File[] files(string $directory, $hidden = false)
 * @method static \Sofiakb\Filesystem\File[] directories(string $directory)
 * @method static string eol(string $filepath)
 * @see Filesystem
 */
class File extends Facade
{
}