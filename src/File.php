<?php


namespace Sofiakb\Filesystem;


use Exception;

/**
 * Class File
 * @package Sofiakb\Filesystem
 *
 * @method static bool exists()
 * @method static bool missing()
 * @method static string get(bool $lock = false)
 * @method static int lines()
 * @method static string hash()
 * @method static int put(string $contents, bool $lock = false)
 * @method static void replace(string $content)
 * @method static int prepend(string $data)
 * @method static int append(string $data)
 * @method static mixed chmod($mode = null)
 * @method static bool delete()
 * @method static bool move(string $target)
 * @method static bool rename(string $target)
 * @method static bool copy(string $target)
 * @method static void|bool link(string $target, string $link)
 * @method static string name()
 * @method static string basename()
 * @method static string dirname()
 * @method static string extension()
 * @method static string type()
 * @method static string mimeType()
 * @method static int size(bool $humanize = false)
 * @method static int lastModified()
 * @method static bool isDirectory()
 * @method static bool isReadable()
 * @method static bool isWritable()
 * @method static bool isFile()
 * @method static bool makeDirectory($mode = 0755, $recursive = false, $force = false)
 * @method static bool moveDirectory(string $to, $overwrite = false)
 * @method static bool copyDirectory(string $destination, $options = null)
 * @method static bool deleteDirectory($preserve = false)
 * @method static bool deleteDirectories()
 * @method static bool cleanDirectory()
 * @method static \Sofiakb\Filesystem\File[] files($hidden = false)
 * @method static \Sofiakb\Filesystem\File[] directories()
 * @method static string eol(string $filepath)
 * @method static int $rows(string $filepath)
 * @see Filesystem
 */
class File
{

    /**
     * @var string $path
     */
    private $path;

    /**
     * @var string $path
     */
    private $name;

    /**
     * File constructor.
     * @param string $path
     * @param bool $directory
     */
    public function __construct($path, bool $directory = false)
    {
        $this->path = $directory ? Facades\File::dirname($path) : $path;
        $this->name = Facades\File::basename($path);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (method_exists(Filesystem::class, $name)) {
            return call_user_func_array(array($this->filesystem(), $name), array_merge([$this->path], $arguments));
        } else throw new Exception("Method [$name] not found in " . Filesystem::class);
    }

    /**
     * Get the filesystem singleton
     *
     * @return Filesystem|null
     */
    private function filesystem()
    {
        return Filesystem::getInstance();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}