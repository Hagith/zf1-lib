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
 * @package     Modern_Image
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Loader */
require_once('Zend/Loader.php');

/**
 * Image adapter factory.
 *
 * @category    Modern
 * @package     Modern_Image
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Image
{
    /**
     * Static method for instantiation of image adapters.
     *
     * @param string $adapter Adapter name
     * @param array|Zend_Config $config Adapter configuration
     * @return Modern_Image_Adapter
     */
    public static function factory($adapter, $config = null)
    {
        $class = 'Modern_Image_Adapter_' . ucfirst($adapter);
        try {
            Zend_Loader::loadClass($class);
        } catch (Exception $e) {
            require_once 'Modern/Image/Exception.php';
            throw new Modern_Image_Exception("Unknown image adapter '$adapter'");
        }

        $adapter = new $class($config);

        // Check if class implements abstract image adapter
        if (!$adapter instanceof Modern_Image_Adapter) {
            require_once 'Modern/Image/Exception.php';
            throw new Modern_Image_Exception("Image adapter '$class' must inherit from Modern_Image_Adapter");
        }

        return $adapter;
    }

}
