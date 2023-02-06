<?php


namespace Sofiakb\Filesystem;


use DirectoryIterator;

/**
 * Class Finder
 * @package Sofiakb\Filesystem
 */
class Finder
{

    /**
     * @var Finder|null
     */
    private static ?Finder $instance = null;

    /**
     * @var string $directory
     */
    private string $directory;

    /**
     * @var bool $ignoreDotFiles
     */
    private bool $ignoreDotFiles = false;

    /**
     * @var array $files
     */
    private array $files;

    private function __construct()
    {
    }

    /**
     * @return Finder|null
     */
    public static function getInstance(): Finder
    {
        if (is_null(self::$instance))
            self::$instance = new Finder();
        return self::$instance;
    }

    /**
     * @return Finder|null
     */
    public static function create(): ?Finder
    {
        self::$instance = null;
        return self::getInstance();
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function in(string $directory): Finder
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @param bool $ignore
     * @return $this
     */
    public function ignoreDotFiles(bool $ignore): Finder
    {
        $this->ignoreDotFiles = $ignore;
        return $this;
    }

    /**
     * @return $this
     */
    public function files(): Finder
    {
        if (isset($this->directory)) {
            $this->files = [];
            $files = new DirectoryIterator($this->directory);

            foreach ($files as $fileInfo) {
                if (!$fileInfo->isFile() || ($this->ignoreDotFiles && $fileInfo->isDot())) continue;
                $this->files[] = new File($fileInfo->getRealPath());
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function directories(): Finder
    {
        if (isset($this->directory)) {
            $this->files = [];
            $files = scandir($this->directory);

            foreach ($files as $item) {
                if (!in_array($item, ['.', '..']) && is_dir(($filepath = $this->directory . DIRECTORY_SEPARATOR . $item)))
                    $this->files[] = new File($filepath, true);
            }
        }
        return $this;
    }

    /**
     * @param bool $case
     * @return $this
     */
    public function sortByName(bool $case = false): Finder
    {
        usort($this->files, function ($file1, $file2) use ($case) {
            return $case
                ? strcasecmp($file1->getName(), $file2->getName())
                : strcmp($file1->getName(), $file2->getName());
        });
        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->files;
    }

}