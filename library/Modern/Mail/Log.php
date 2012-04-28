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
 * @package     Modern_Mail
 * @subpackage  Log
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Modern_Mail */
require_once('Modern/Mail.php');

/**
 * Log dla Modern_Mail - zapisuje w dzienniku
 * całą komunikację klient-serwer odbywającej się
 * podczas wysyłi e-maila. Log dopisuje te dane
 * do wskazanego pliku.
 * Klasa implementuje wzorzec singleton.
 *
 * @category    Modern
 * @package     Modern_Mail
 * @subpackage  Log
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Mail_Log
{
    /**
     * Instancja obiektu tej klasy.
     *
     * @var Modern_Mail_Log
     */
    protected static $_instance = null;

    /**
     * Ścieżka do pliku z logiem
     *
     * @var string|null
     */
    protected $_fileLogPath = null;

    /**
     * Prywatny konstruktor.
     *
     */
    private function __construct()
    {
    }

    /**
     * Metoda zwraca obiekt będący instancją tej klasy.
     *
     * @return Modern_Mail_Log
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Metoda ustawia ścieżkę do pliku dziennika.
     *
     * @param string $path
     */
    public function setFileLogPath($path)
    {
        if(!file_exists(dirname($path))) {
            /** Modern_Mail_Log_Exception */
            require_once('Modern/Mail/Log/Exception.php');
            throw new Modern_Mail_Log_Exception('Modern_Mail_Log: Katalog dla pliku dziennika maili nie istnieje.');
        }

        if(null === $this->_fileLogPath) {
            $this->_fileLogPath = $path;
        }
    }

    /**
     * Metoda zapisuje log z wysyłki przeprowadzonej
     * przez obiekt klasy Modern_Mail.
     *
     * @param Modern_Mail $mail
     */
    public function log(Modern_Mail $mail)
    {
        if(null === $this->_fileLogPath) {
            throw new Exception('Modern_Mail_Log: Nie ustawiono ścieżki dla pliku dziennika. Spróbuj przed wywołaniem tej metody użyć metody Modern_Mail_Log::setFileLogPath()');
        }
        $log = $mail->getLog();
        if(strlen($log) > 0) {
            $data = "--- " . date('Y-m-d H:i:s') . " ---\n\n" . $log;
            $fp = fopen($this->_fileLogPath, 'a+');
            fwrite($fp, $data);
            fclose($fp);
        }
    }

}
