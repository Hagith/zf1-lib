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
 * Basic file operations.
 *
 * @category    Modern
 * @package     Modern_Filesystem
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filesystem_File
{
    /**
     * File path.
     *
     * @var string
     */
    protected $_path;

    /**
     * File content
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Konstruktor. Otwiera plik $filename jeśli podano.
     *
     * @param string $path
     */
    public function __construct($path = null)
    {
        if (null !== $path) {
            $this->open($path);
        }
    }

    /**
     * Ustawia nazwę pliku.
     *
     * @param string $name
     * @return Modern_Filesystem_File
     */
    public function setPath($name)
    {
        if (!is_string($name) || strlen($name) === 0) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Nazwa pliku musi być nie pustym ciągiem znaków');
        }

        $this->_path = $name;

        return $this;
    }

    /**
     * Zwraca nazwę pliku.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Ustawia treść pliku.
     *
     * @param string $content
     * @return Modern_Filesystem_File
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Treść musi być ciągiem znaków');
        }
        $this->_content = $content;

        return $this;
    }

    /**
     * Zwraca zawartość pliku.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Dodaje treść na koniec pliku.
     *
     * @param string $content
     * @return Modern_Filesystem_File
     */
    public function appendContent($content)
    {
        if (!is_string($content)) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Treść musi być ciągiem znaków');
        }
        $this->_content .= $content;

        return $this;
    }

    /**
     * Dodaje treść na początek pliku.
     *
     * @param string $content
     * @return Modern_Filesystem_File
     */
    public function prependContent($content)
    {
        if (!is_string($content)) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception('Treść musi być ciągiem znaków');
        }
        $this->_content = $content . $this->_content;

        return $this;
    }

    /**
     * Otwiera określony plik.
     *
     * Jeśli podano parametr $filename otwiera wskazany plik
     * w przeciwnym przypadku otwiera $this->_filename.
     *
     * @param string $filename
     * @return Modern_Filesystem_File
     */
    public function open($filename = null)
    {
        if (null !== $filename) {
            $this->setPath($filename);
        }

        $result = file_get_contents($this->_path);
        if (false === $result) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Plik '{$this->_name}' nie istnieje lub brak praw odczytu");
        }

        $this->setContent($result);

        return $this;
    }

    /**
     * Zapisuje treść do pliku.
     *
     * @return Modern_Filesystem_File
     */
    public function save()
    {
        if (null == $this->_path) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Nie ustawiono nazwy pliku");
        }
        if (false === file_put_contents($this->_path, $this->_content)) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Błąd zapisu do pliku '{$this->_name}'");
        }

        return $this;
    }

    /**
     * Czyści własności _name i _content.
     *
     * @return Modern_Filesystem_File
     */
    public function clear()
    {
        $this->_path = null;
        $this->_content = '';

        return $this;
    }

    /**
     * Usuwa określony plik.
     *
     * @param string $filename
     * @return Modern_Filesystem_File
     */
    public function delete($filename = null)
    {
        if (null !== $filename) {
            $this->setPath($filename);
        }
        if (!$this->exists()) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Plik '{$this->_name}' nie istnieje");
        }
        if (!unlink($this->_path)) {
            /** @see Modern_Filesystem_Exception */
            require_once('Modern/Filesystem/Exception.php');
            throw new Modern_Filesystem_Exception("Błąd usuwania pliku '{$this->_name}'");
        }
        $this->clear();

        return $this;
    }

    /**
     * Sprawdza czy określony plik istnieje.
     *
     * @param string $filename
     * @return boolean
     */
    public function exists($filename = null)
    {
        if (null !== $filename) {
            $this->setPath($filename);
        }

        return file_exists($this->getPath());
    }

    /**
     * Get random unique file name in given directory.
     *
     * @param string $dir
     * @param string $suffix
     * @return string
     * @uses Modern_String
     */
    public static function getRandomName($dir, $suffix = '')
    {
        if (!is_dir($dir)) {
            throw new Modern_Filesystem_Exception("Katalog '$dir', nie istnieje.");
        }

        while (true) {
            $name = strtolower(Modern_String::random(6) . $suffix);
            $file = $dir . DIRECTORY_SEPARATOR . $name;
            if (!is_file($file)) {
                return $name;
            }
        }
    }

    /**
     * @param string $dir
     * @param string $filename
     * @return string
     * @throws Modern_Filesystem_Exception
     */
    public static function getUniqueName($dir, $filename)
    {
        if (!is_dir($dir)) {
            throw new Modern_Filesystem_Exception("Directory '$dir' doesn't exists");
        }

        $originalFilename = $filename;
        $count = 1;

        if ($dir == '/') {
            $dir = '';
        }

        while (true) {
            $file = new self($dir . $filename);
            if (Asset_Service::pathExists($dir . '/' . $filename)) {
                $filename = str_replace(
                    '.' . Pimcore_File::getFileExtension($originalFilename),
                    '_' . $count . '.' . Pimcore_File::getFileExtension($originalFilename),
                    $originalFilename
                );
                $count++;
            } else {
                return $filename;
            }
        }
    }

}
