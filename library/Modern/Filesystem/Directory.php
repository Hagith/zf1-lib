<?php

/**
 * ModernWeb
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modernweb.pl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@modernweb.pl so we can send you a copy immediately.
 *
 * @category    Modern
 * @package     Modern_Filesystem
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Class for directory operations.
 *
 * @category    Modern
 * @package     Modern_Filesystem
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filesystem_Directory
{
    /**
     * Current directory path.
     *
     * @var string
     */
    protected $_path;

    /**
     * Current directory itarator.
     *
     * @var DirectoryIterator
     */
    protected $_iterator;

    /**
     * Create directory instance.
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        if(null !== $path) {
            $this->setPath($path);

            if(!$this->exists()) {
                /** @see Modern_Filesystem_Exception */
                require_once('Modern/Filesystem/Exception.php');
                throw new Modern_Filesystem_Exception("Directory '{$this->_path}' doesn't exists");
            }
        }
    }

    /**
     * Set current directory path.
     *
     * @param string $path
     * @return Modern_Filesystem_Directory
     */
    public function setPath($path)
    {
        if(!is_string($path) || strlen($path) === 0) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Directory path cannot be empty');
        }
        $this->_path = $path;
        $this->_iterator = null;
        return $this;
    }

    /**
     * Get current directory path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Get current directory iterator.
     *
     * @return DirectoryIterator
     */
    public function getIterator()
    {
        if(null == $this->_path) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Set current directory path first');
        }

        if(!$this->exists()) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Directory '$this->_path' doesn't exists");
        }

        if(null === $this->_iterator) {
            $this->_iterator = new DirectoryIterator($this->_path);
        }

        return $this->_iterator;
    }

    /**
     * Create directory in given path.
     *
     * @param string $path
     * @param integer $mode
     * @param boolean $recursive
     * @return Modern_Filesystem_Directory
     */
    public function create($path = null, $mode = 0755, $recursive = false)
    {
        if(null !== $path) {
            $this->setPath($path);
        }

        if(null == $this->_path) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Directory path not set');
        }

        // do nothing if directory already exists
        if($this->exists()) {
            return $this;
        }

        $umask = umask(0);
        $result = @mkdir($this->_path, $mode, $recursive);
        umask($umask);

        if(!$result) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Can't create '$this->_path' directory, check filesystem permissions");
        }

        return $this;
    }

    /**
     * Remove directory contents recursively.
     *
     * @param string $path
     * @param boolean $followSymlinks
     * @return Modern_Filesystem_Directory
     */
    public function deleteContents($path = null, $followSymlinks = false)
    {
        if(null !== $path) {
            $this->setPath($path);
        }

        if($this->isEmpty()) {
            return $this;
        }

        $iterator = $this->getIterator();

        foreach ($iterator as $fileinfo) {
            if($fileinfo->isDot()) {
                continue;
            }
            if ($fileinfo->isFile() || ($fileinfo->isLink() && !$followSymlinks)) {
                unlink($fileinfo->getPathname());
            }
            if($fileinfo->isDir() || ($fileinfo->isLink() && $followSymlinks)) {
                $dir = new self();
                $dir->deleteContents($fileinfo->getPathname(), $followSymlinks);
                if($fileinfo->isLink()) {
                    unlink($fileinfo->getPathname());
                } else {
                    rmdir($fileinfo->getPathname());
                }
            }
        }

        return $this;
    }

    /**
     * Remove current directory with all contents.
     *
     * @param string $path
     * @param boolean $followSymlinks
     * @return Modern_Filesystem_Directory
     */
    public function delete($path = null, $followSymlinks = false)
    {
        $this->deleteContents($path, $followSymlinks);

        rmdir($this->_path);

        return $this;
    }

    /**
     * Rename directory.
     *
     * @param string $newName
     * @param string $path
     * @return \Modern_Filesystem_Directory
     * @throws Modern_Filesystem_Exception
     */
    public function rename($newName, $path = null)
    {
        if(null !== $path) {
            $this->setPath($path);
        }

        if(rename($this->getPath(), $newName)) {
            $this->setPath($newName);
        } else {
            throw new Modern_Filesystem_Exception(
                "Cannot rename directory '{$this->getPath()}' to '$newName'"
            );
        }
        return $this;
    }

    /**
     * Check if given/current directory exists.
     *
     * @param string $path
     * @return boolean
     */
    public function exists($path = null)
    {
        if(null !== $path) {
            $this->setPath($path);
        }
        return is_dir($this->_path);
    }

    /**
     * Check if given/current directory is empty.
     *
     * @param string $path
     * @return boolean
     */
    public function isEmpty($path = null)
    {
        if(null !== $path) {
            $this->setPath($path);
        }
        foreach ($this->getIterator() as $fileinfo) {
            if(!$fileinfo->isDot()) {
                return false;
            }
        }
        return true;
    }

}
