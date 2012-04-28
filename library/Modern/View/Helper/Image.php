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
 * @package     Modern_View
 * @subpackage  Helper
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/** @see Zend_View_Helper_Abstract */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper do konwersji obrazków na miniaturki.
 *
 * @category    Modern
 * @package     Modern_View
 * @subpackage  Helper
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_View_Helper_Image extends Zend_View_Helper_Abstract
{
    /**
     * Zwraca adres do miniaturki danego typu zdefiniowanego
     * w konfiguracji {@see Modern_Image_Manager}.
     *
     * @param  string $source Adres pliku źródłowego
     * @param  string $type Typ miniatury zadeklarowany w konfiguracji manager'a
     * @return string
     */
    public function image($source, $type)
    {
        $manager = Modern_Application::getInstance()->getResource('image');
        if(null === $manager) {
            return '';
        }
        return $manager->get($source, $type);
    }
}
