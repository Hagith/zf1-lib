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
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Application_Resource_ResourceAbstract */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Zend application resource provides Modern_Image_Manager.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_Image extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Image manager instance.
     *
     * @var Modern_Image_Manager
     */
    protected $_manager;

    /**
     * Initialize resource.
     *
     * @return Modern_Image_Manager
     */
    public function init()
    {
        $this->getBootstrap()->bootstrap('FrontController');
        return $this->getImageManager();
    }

    /**
     * Retuns image manager instance.
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
