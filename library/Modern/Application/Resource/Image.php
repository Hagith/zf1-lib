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
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/**
 * Zasób aplikacji dostarczający komponent Modern_Image_Manager.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_Image extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Manager obrazków.
     *
     * @var Modern_Image_Manager
     */
    protected $_manager;

    /**
     * Inicjuje zasób.
     *
     * @return Modern_Image_Manager
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('FrontController');
        return $this->getImageManager();
    }

    /**
     * Zwraca obiekt managera miniatur.
     *
     * @return Modern_Image_Manager
     */
    public function getImageManager()
    {
        if (null === $this->_manager) {
            $this->_manager = new Modern_Image_Manager($this->getOptions());
        }
        return $this->_manager;
    }
}