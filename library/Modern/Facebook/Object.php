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
 * @package     Modern_Facebook
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Base class for Facebook API objects.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
abstract class Modern_Facebook_Object
{
    /**
     * Modern_Facebook
     *
     * @var object
     */
    static protected $_app;

    /**
     * Ustawia Modern_Facebook
     *
     * @param Modern_Facebook $app
     * @return void
     */
    static public function setApp(Modern_Facebook $app)
    {
        self::$_app = $app;
    }

    /**
     * zwraca Modern_Facebook
     *
     * @return Modern_Facebook
     */
    static public function getApp()
    {
        return self::$_app;
    }

}
