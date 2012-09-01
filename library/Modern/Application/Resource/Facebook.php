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
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_Facebook extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Facebook_Model_Facade
     */
    protected $_facebook;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Facebook_Model_Facade
     */
    public function init()
    {
        return $this->getFacebook();
    }

    /**
     * Zwraca obiekt Facebook_Model_Facade
     *
     * @return Facebook_Model_Facade
     */
    public function getFacebook()
    {
        if (null === $this->_facebook) {

            $option = $this->getOptions();
            $this->_facebook = new Modern_Facebook($option);

            // dodanie fasady jako zmiennej globalnej w widoku
            $bootstrap = $this->getBootstrap();
            $bootstrap->bootstrap('view');
            $view = $bootstrap->getResource('view');
            $view->addGlobalVar('facebook', $this->_facebook);
        }
        return $this->_facebook;
    }

}