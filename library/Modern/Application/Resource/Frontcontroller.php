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
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Application_Resource_Frontcontroller */
require_once('Zend/Application/Resource/Frontcontroller.php');

/**
 * Klasa stanowi rozszerzenie zasobu FrntController'a.
 * Rozszerzenie umożliwia zarejestrowanie zmodyfikowanego obiektu dispatchera.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    /**
     * Inicjuje Front Controller.
     *
     * @return Zend_Controller_Front
     */
    public function init()
    {
        foreach ($this->getOptions() as $key => $value) {
            switch (strtolower($key)) {
                case 'dispatcherclass':
                    Zend_Loader::loadClass($value);
                    $this->getFrontController()->setDispatcher(new $value());
                    break;
            }
        }

        return parent::init();
    }
}