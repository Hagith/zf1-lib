<?php
/**
 * Modern
 *
 * LICENSE
 *
 * This source file is subject to version 1.0
 * of the ModernWeb license.
 *
 * @category    Modern
 * @package     Modern_Module
 * @subpackage  Facade
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/**
 * Główna klasa do budowania modelu modułu opartego o Modern Framework.
 * Implementacja wzorca fasady.
 *
 * @category    Modern
 * @package     Modern_Module
 * @subpackage  Facade
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
abstract class Modern_Module_Facade
{
    /**
     * Konfiguracja modułu.
     *
     * @var Zend_Config
     */
    protected $_options;

    /**
     * Kontener danych
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Ładuje konfigurację i uruchamia model modułu.
     *
     * @param string|array|Zend_Config $config
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            if (is_string($options)) {
                $this->_options = $this->_loadConfig($options);
            } elseif ($options instanceof Zend_Config) {
                $this->_options = $options;
            } elseif (is_array($options)) {
                $this->_options = new Zend_Config($options);
            } else {
                throw new Modern_Module_Exception(
                    'Nieprawidłowa konfiguracja modułu; należy podać lokalizację'
                    . 'pliku konfiguracyjnego, obiekt Zend_Config, lub tablicę'
                );
            }
        } else {
            $config = APPLICATION_PATH . '/configs/' . $this->getModuleName(true);
            if(is_readable($config . '.ini')) {
                $this->_options = $this->_loadConfig($config . '.ini');
            } else if (is_readable($config . '.xml')) {
                $this->_options = $this->_loadConfig($config . '.xml');
            } else {
                $this->_options = new Zend_Config(array());
            }
        }
        $this->init();
    }

    /**
     * Metoda wywoływana jest jako ostatnia operacja konstruktora.
     * Pozwala na wykonanie dodatkowych operacji inicjujących model
     * konkretnego modułu poprzez przesłonięcie w klasie dziedziczącej.
     *
     */
    public function init()
    {
    }

    /**
     * Zwraca opcje konfiguracyjne modułu.
     *
     * @return Zend_Config
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Zwraca nazwę modułu.
     *
     * @return string
     */
    public function getModuleName($lower = false)
    {
        $class = explode('_', get_class($this), -1);
        $module = isset($class[1]) ? $class[1] : $class[0];

        if($lower) {
            return strtolower($module);
        }

        return $module;
    }

    /**
     * Ustawienie kontenera danych
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * Pobranie kontenera danych
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Ustawienie wartości dla odpowiedniego pola kontenera danych
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * Pobranie wartości pola kontenera danych
     *
     * @param string $name
     * @return mixed
     */
    public function  __get($name)
    {
        if (!isset($this->_data[$name])) {
            return null;
        }
        return $this->_data[$name];
    }

    /**
     * Sprawdza czy istnieje zmienna o podanej nazwie
     * w kontenerze danych.
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Usuwa zmienną o określonej nazwie z kontenera danych.
     *
     * @param string $name
     */
    public function  __unset($name)
    {
        if(isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
    }

    /**
     * Wczytuje konfigurację z pliku.
     *
     * @param  string $file
     * @throws Modern_Module_Exception Gdy podano nieprawidłowy plik konfiguracyjny
     * @return Zend_Config
     */
    protected function _loadConfig($file)
    {
        $environment = Modern_Application::getInstance()
            ->getBootstrap()->getEnvironment();
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($suffix) {
            case 'ini':
                $config = new Zend_Config_Ini($file, $environment);
                break;

            case 'xml':
                $config = new Zend_Config_Xml($file, $environment);
                break;

            case 'php':
            case 'inc':
                $config = include $file;
                if (!is_array($config)) {
                    throw new Modern_Module_Exception(
                        'Nieprawidłowa konfiguracja modułu; Plik PHP nie zwraca tablicy'
                    );
                }
                $config = new Zend_Config($config);
                break;

            default:
                throw new Modern_Module_Exception(
                    'Nieprawidłowa konfiguracja modułu; nieznany typ konfiguracji'
                );
        }

        return $config;
    }
}