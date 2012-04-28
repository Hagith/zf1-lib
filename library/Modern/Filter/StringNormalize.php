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
 * @category    Modern
 * @package     Modern_Filter
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filter_StringNormalize implements Zend_Filter_Interface
{
    /**
     * Implementacja interfejsu Zend_Filter_Interface - właściwa metoda filtrująca
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $string = new Modern_String($value);
        return $string->normalize()->__toString();
    }
}