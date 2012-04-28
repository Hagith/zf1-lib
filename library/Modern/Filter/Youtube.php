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
 * @package     Modern_Filter
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/** @see Zend_Filter_Interface */
require_once 'Zend/Filter/Interface.php';

/**
 * Filtr wyciągający identyfikator filmu z podanego adresu URL do serwisu YouTube.
 *
 * @category    Modern
 * @package     Modern_Filter
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filter_Youtube implements Zend_Filter_Interface
{
    /**
     * Implementacja interfejsu Zend_Filter_Interface - właściwa metoda filtrująca
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $matches = array();
        preg_match('/[\/\?]v[=\/]([\w-]+)/', $value, $matches);
        return (isset($matches[1]) && !empty($matches[1])) ? $matches[1] : $value;
    }
}