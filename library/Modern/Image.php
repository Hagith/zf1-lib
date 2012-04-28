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
 * @package     Modern_Image
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/** @see Zend_Loader */
require_once('Zend/Loader.php');

/**
 * Klasa fabrykująca odpowiedni adapter.
 *
 * @category    Modern
 * @package     Modern_Image
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Image
{
    /**
     * Metoda statyczna tworząca obiekt adaptera określonej biblioteki graficznej.
     *
     * @param   string $adapter Identyfikator adaptera
     * @param   array|Zend_Config $config Tablica konfiguracyjna lub obiekt Zend_Config
     * @return  Modern_Image_Adapter Obiekt adaptera biblioteki graficznej
     */
    public static function factory($adapter, $config = null)
    {
        $adapterClass = 'Modern_Image_Adapter_' . ucfirst($adapter);
        try {
            Zend_Loader::loadClass($adapterClass);
        } catch (Exception $e) {
            /** @see Modern_Image_Exception */
            require_once('Modern/Image/Exception.php');
            throw new Modern_Image_Exception("Nieznany adapter '$adapter'");
        }

        $imgAdapter = new $adapterClass($config);

        // Sprawdzenie, czy klasa implementuje adapter bazowy
        if(!$imgAdapter instanceof Modern_Image_Adapter) {
            /** @see Modern_Image_Exception */
            require_once('Modern/Image/Exception.php');
            throw new Modern_Image_Exception('Adapter musi dziedziczyć po Modern_Image_Adapter.');
        }

        return $imgAdapter;
    }
}