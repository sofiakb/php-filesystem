<?php

use Sofiakb\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

/**
 * Class FilesystemTest
 */
class FilesystemTest extends TestCase
{
    /**
     * @var Filesystem|null
     */
    private ?Filesystem $filesystem;
    /**
     * @var string
     */
    private string $path;
    /**
     * @var string
     */
    private string $file;
    /**
     * @var false|string
     */
    private string|false $hash;

    /**
     * FilesystemTest constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->filesystem = Filesystem::getInstance();
        $this->path = __DIR__ . DIRECTORY_SEPARATOR . 'folder';
        $this->file = $this->path . DIRECTORY_SEPARATOR . 'test.txt';
        $this->hash = md5_file($this->file);
        if (!file_exists($this->path))
            @mkdir($this->path);

        parent::__construct($name ?? get_class($this), $data, $dataName);
    }

    /**
     *
     */
    public function test()
    {
        $this->exists();
        $this->missing();

        $put = $this->put();
        $prepend = $this->prepend(strlen($put));
        $append = $this->append();
        $this->get($prepend . $put . $append);
        $this->lines();
        $this->hash();
        $this->chmod();
        $this->copy();
        $this->name();
        $this->basename();
        $this->extension();
        $this->dirname();
        $this->mimeType();
        $this->type();
        $this->isDirectory();
        $this->isFile();
        $this->isFileReadable();
        $this->isFileWritable();
    }

    /**
     *
     */
    private function exists()
    {
        $this->assertTrue($this->filesystem->exists($this->path));
    }

    /**
     *
     */
    private function missing()
    {
        $this->assertFalse($this->filesystem->missing($this->path));
    }

    /**
     * @return string
     */
    private function put()
    {
        $content = "Je suis du contenu put";
        $this->assertEquals(strlen($content), $this->filesystem->put($this->file, $content));
        return $content;
    }

    /**
     * @param int $previous
     * @return string
     * @throws \Sofiakb\Filesystem\Exceptions\FileNotFoundException
     */
    private function prepend($previous = 0)
    {
        $content = "Je suis du contenu prepend";
        $this->assertEquals(strlen($content) + $previous, $this->filesystem->prepend($this->file, $content));
        return $content;
    }

    /**
     * @return string
     */
    private function append()
    {
        $content = "Je suis du contenu append";
        $this->assertEquals(strlen($content), $this->filesystem->append($this->file, $content));
        return $content;
    }

    /**
     * @param $content
     * @throws \Sofiakb\Filesystem\Exceptions\FileNotFoundException
     */
    private function get($content)
    {
        $this->assertEquals($content, $this->filesystem->get($this->file));
    }

    /**
     * @throws \Sofiakb\Filesystem\Exceptions\FileNotFoundException
     */
    private function lines()
    {
        $this->assertEquals(1, $this->filesystem->lines($this->file));
    }

    /**
     *
     */
    private function hash()
    {
        $this->assertEquals($this->hash, $this->filesystem->hash($this->file));
    }

    /**
     *
     */
    private function chmod()
    {
        $this->assertTrue($this->filesystem->chmod($this->file, 0777));
        sleep(1);
        $this->assertEquals('0777', $this->filesystem->chmod($this->file));
    }

    /**
     *
     */
    private function copy()
    {
        $this->assertTrue($this->filesystem->copy($this->file, $this->path . DIRECTORY_SEPARATOR . 'test2.txt'));
    }

    /**
     *
     */
    private function name()
    {
        $this->assertEquals('test', $this->filesystem->name($this->file));
    }

    /**
     *
     */
    private function basename()
    {
        $this->assertEquals('test.txt', $this->filesystem->basename($this->file));
    }

    /**
     *
     */
    private function extension()
    {
        $this->assertEquals('txt', $this->filesystem->extension($this->file));
    }

    /**
     *
     */
    private function mimeType()
    {
        $this->assertEquals('text/plain', $this->filesystem->mimeType($this->file));
    }

    /**
     *
     */
    private function type()
    {
        $this->assertEquals('file', $this->filesystem->type($this->file));
    }

    /**
     *
     */
    private function dirname()
    {
        $this->assertEquals($this->path, $this->filesystem->dirname($this->file));
    }

    /**
     *
     */
    private function isDirectory()
    {
        $this->assertTrue($this->filesystem->isDirectory($this->path));
        $this->assertFalse($this->filesystem->isDirectory($this->file));
    }

    /**
     *
     */
    private function isFileReadable()
    {
        $this->assertTrue($this->filesystem->isReadable($this->file));
    }

    /**
     *
     */
    private function isFileWritable()
    {
        $this->assertTrue($this->filesystem->isWritable($this->file));
    }

    /**
     *
     */
    private function isFile()
    {
        $this->assertFalse($this->filesystem->isFile($this->path));
        $this->assertTrue($this->filesystem->isFile($this->file));
    }

    /*
     bool delete($paths)
     bool move(string $path, string $target)
     bool rename(string $path, string $target)
     bool makeDirectory(string $path, $mode = 0755, $recursive = false, $force = false)
     bool moveDirectory(string $from, string $to, $overwrite = false)
     bool copyDirectory(string $directory, string $destination, $options = null)
     bool deleteDirectory(string $directory, $preserve = false)
     bool deleteDirectories(string $directory)
     bool cleanDirectory(string $directory)
     \Sofiakb\Filesystem\File[] files(string $directory, $hidden = false)
     \Sofiakb\Filesystem\File[] directories(string $directory)*/
}
