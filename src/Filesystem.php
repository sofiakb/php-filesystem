<?php


namespace Sofiakb\Filesystem;


use ErrorException;
use FilesystemIterator;
use SplFileObject;
use Sofiakb\Filesystem\Exceptions\FileNotFoundException;
use Sofiakb\Filesystem\Tools\Helpers;

/**
 * Class Filesystem
 * @package Sofiakb\Filesystem
 */
class Filesystem
{
    
    /**
     * @var Filesystem null
     */
    private static $instance = null;
    
    /**
     * Filesystem constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * @return static|null
     */
    public static function getInstance()
    {
        if (is_null(self::$instance))
            self::$instance = new static;
        return self::$instance;
    }
    
    /**
     * Determine if a file or directory exists.
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }
    
    /**
     * Determine if a file or directory is missing.
     *
     * @param string $path
     * @return bool
     */
    public function missing(string $path): bool
    {
        return !$this->exists($path);
    }
    
    /**
     * Get the contents of a file.
     *
     * @param string $path
     * @param bool $lock
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function get(string $path, bool $lock = false)
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }
        
        throw new FileNotFoundException("File does not exist at path {$path}.");
    }
    
    /**
     * Get contents of a file with shared access.
     *
     * @param string $path
     * @return string
     */
    private function sharedGet(string $path)
    {
        $contents = '';
        
        $handle = fopen($path, 'rb');
        
        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);
                    
                    $contents = fread($handle, $this->size($path) ?: 1);
                    
                    flock($handle, LOCK_UN);
                }
            }
            finally {
                fclose($handle);
            }
        }
        
        return $contents;
    }
    
    /**
     * Get the contents of a file one line at a time.
     *
     * @param string $path
     * @return int
     *
     * @throws FileNotFoundException
     */
    public function lines(string $path)
    {
        if (!$this->isFile($path)) {
            throw new FileNotFoundException(
                "File does not exist at path {$path}."
            );
        }
        
        $file = new SplFileObject($path);
        
        $file->setFlags(SplFileObject::DROP_NEW_LINE);
        $lines = 0;
        
        while (!$file->eof()) {
            $file->fgets();
            $lines++;
        }
        return $lines;
    }
    
    /**
     * Get the MD5 hash of the file at the given path.
     *
     * @param string $path
     * @return string
     */
    public function hash(string $path)
    {
        return md5_file($path);
    }
    
    /**
     * Write the contents of a file.
     *
     * @param string $path
     * @param string $contents
     * @param bool $lock
     * @return int|bool
     */
    public function put(string $path, string $contents, bool $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }
    
    /**
     * Write the contents of a file, replacing it atomically if it already exists.
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    public function replace(string $path, string $content)
    {
        // If the path already exists and is a symlink, get the real path...
        clearstatcache(true, $path);
        
        $path = realpath($path) ?: $path;
        
        $tempPath = tempnam(dirname($path), basename($path));
        
        // Fix permissions of tempPath because `tempnam()` creates it with permissions set to 0600...
        chmod($tempPath, 0777 - umask());
        
        file_put_contents($tempPath, $content);
        
        rename($tempPath, $path);
    }
    
    /**
     * Prepend to a file.
     *
     * @param string $path
     * @param string $data
     * @return int
     * @throws FileNotFoundException
     */
    public function prepend(string $path, string $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        }
        
        return $this->put($path, $data);
    }
    
    /**
     * Append to a file.
     *
     * @param string $path
     * @param string $data
     * @return int
     */
    public function append(string $path, string $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }
    
    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param string $path
     * @param int|null $mode
     * @return mixed
     */
    public function chmod(string $path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }
        
        return substr(sprintf('%o', fileperms($path)), -4);
    }
    
    /**
     * Delete the file at a given path.
     *
     * @param string|array $paths
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        
        $success = true;
        
        foreach ($paths as $path) {
            try {
                if (!@unlink($path)) {
                    $success = false;
                }
            }
            catch (ErrorException $e) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Move a file to a new location.
     *
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function move(string $path, string $target)
    {
        return rename($path, $target);
    }
    
    /**
     * Move a file to a new location.
     *
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function rename(string $path, string $target)
    {
        return $this->move($path, $target);
    }
    
    /**
     * Copy a file to a new location.
     *
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function copy(string $path, string $target)
    {
        return copy($path, $target);
    }
    
    /**
     * Create a symlink to the target file or directory. On Windows, a hard link is created if the target is a file.
     *
     * @param string $target
     * @param string $link
     * @return bool|void
     */
    public function link(string $target, string $link)
    {
        if (!Helpers::windows_os()) {
            return symlink($target, $link);
        }
        
        $mode = $this->isDirectory($target) ? 'J' : 'H';
        
        exec("mklink /{$mode} " . escapeshellarg($link) . ' ' . escapeshellarg($target));
    }
    
    /**
     * Extract the file name from a file path.
     *
     * @param string $path
     * @return string
     */
    public function name(string $path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }
    
    /**
     * Extract the trailing name component from a file path.
     *
     * @param string $path
     * @return string
     */
    public function basename(string $path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }
    
    /**
     * Extract the parent directory from a file path.
     *
     * @param string $path
     * @return string
     */
    public function dirname(string $path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }
    
    /**
     * Extract the file extension from a file path.
     *
     * @param string $path
     * @return string
     */
    public function extension(string $path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    
    /**
     * Get the file type of a given file.
     *
     * @param string $path
     * @return string
     */
    public function type(string $path)
    {
        return filetype($path);
    }
    
    /**
     * Get the mime-type of a given file.
     *
     * @param string $path
     * @return string|false
     */
    public function mimeType(string $path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }
    
    /**
     * Get the file size of a given file.
     *
     * @param string $path
     * @param bool $humanize
     * @return int
     */
    public function size(string $path, bool $humanize = false)
    {
        return $humanize ? Helpers::humanizeSize(filesize($path)) : filesize($path);
    }
    
    /**
     * Get the file's last modification time.
     *
     * @param string $path
     * @return int
     */
    public function lastModified(string $path)
    {
        return filemtime($path);
    }
    
    /**
     * Determine if the given path is a directory.
     *
     * @param string $directory
     * @return bool
     */
    public function isDirectory(string $directory)
    {
        return is_dir($directory);
    }
    
    
    /**
     * Determine if the given path is readable.
     *
     * @param string $path
     * @return bool
     */
    public function isReadable(string $path)
    {
        return is_readable($path);
    }
    
    /**
     * Determine if the given path is writable.
     *
     * @param string $path
     * @return bool
     */
    public function isWritable(string $path)
    {
        return is_writable($path);
    }
    
    /**
     * Determine if the given path is a file.
     *
     * @param string $file
     * @return bool
     */
    public function isFile(string $file)
    {
        return is_file($file);
    }
    
    
    /**
     * Get an array of all files in a directory.
     *
     * @param string $directory
     * @param bool $hidden
     * @return File[]
     */
    public function files(string $directory, $hidden = false)
    {
        return Finder::create()
            ->in($directory)
            ->ignoreDotFiles(!$hidden)
            ->files()
            ->sortByName()
            ->get();
    }
    
    /**
     * Get all of the directories within a given directory.
     *
     * @param string $directory
     * @return File[]
     */
    public function directories(string $directory)
    {
        return Finder::create()
            ->in($directory)
            ->directories()
            ->sortByName()
            ->get();
    }
    
    /**
     * Ensure a directory exists.
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return void
     */
    public function ensureDirectoryExists(string $path, $mode = 0755, $recursive = true)
    {
        if (!$this->isDirectory($path)) {
            $this->makeDirectory($path, $mode, $recursive);
        }
    }
    
    /**
     * Create a directory.
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @param bool $force
     * @return bool
     */
    public function makeDirectory(string $path, $mode = 0755, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        
        return mkdir($path, $mode, $recursive);
    }
    
    /**
     * Move a directory.
     *
     * @param string $from
     * @param string $to
     * @param bool $overwrite
     * @return bool
     */
    public function moveDirectory(string $from, string $to, $overwrite = false)
    {
        if ($overwrite && $this->isDirectory($to) && !$this->deleteDirectory($to)) {
            return false;
        }
        
        return @rename($from, $to) === true;
    }
    
    /**
     * Copy a directory from one location to another.
     *
     * @param string $directory
     * @param string $destination
     * @param int|null $options
     * @return bool
     */
    public function copyDirectory(string $directory, string $destination, $options = null)
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }
        
        $options = $options ?: FilesystemIterator::SKIP_DOTS;
        
        // If the destination directory does not actually exist, we will go ahead and
        // create it recursively, which just gets the destination prepared to copy
        // the files over. Once we make the directory we'll proceed the copying.
        $this->ensureDirectoryExists($destination, 0777);
        
        $items = new FilesystemIterator($directory, $options);
        
        foreach ($items as $item) {
            // As we spin through items, we will check to see if the current file is actually
            // a directory or a file. When it is actually a directory we will need to call
            // back into this function recursively to keep copying these nested folders.
            $target = $destination . '/' . $item->getBasename();
            
            if ($item->isDir()) {
                $path = $item->getPathname();
                
                if (!$this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            }
            
            // If the current items is just a regular file, we will just copy this to the new
            // location and keep looping. If for some reason the copy fails we'll bail out
            // and return false, so the developer is aware that the copy process failed.
            else {
                if (!$this->copy($item->getPathname(), $target)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Recursively delete a directory.
     *
     * The directory itself may be optionally preserved.
     *
     * @param string $directory
     * @param bool $preserve
     * @return bool
     */
    public function deleteDirectory(string $directory, $preserve = false)
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }
        
        $items = new FilesystemIterator($directory);
        
        foreach ($items as $item) {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && !$item->isLink()) {
                $this->deleteDirectory($item->getPathname());
            }
            
            // If the item is just a file, we can go ahead and delete it since we're
            // just looping through and waxing all of the files in this directory
            // and calling directories recursively, so we delete the real path.
            else {
                $this->delete($item->getPathname());
            }
        }
        
        if (!$preserve) {
            @rmdir($directory);
        }
        
        return true;
    }
    
    /**
     * Remove all of the directories within a given directory.
     *
     * @param string $directory
     * @return bool
     */
    public function deleteDirectories(string $directory)
    {
        $allDirectories = $this->directories($directory);
        
        if (!empty($allDirectories)) {
            foreach ($allDirectories as $directoryName) {
                $this->deleteDirectory($directoryName);
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Empty the specified directory of all files and folders.
     *
     * @param string $directory
     * @return bool
     */
    public function cleanDirectory(string $directory)
    {
        return $this->deleteDirectory($directory, true);
    }
    
    /**
     * Detect file eol.
     *
     * @param string $filepath
     * @return bool|string
     */
    public function eol(string $filepath)
    {
        return Eol::detectFileEOL($filepath);
    }
    
}