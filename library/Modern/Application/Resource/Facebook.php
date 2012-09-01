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
     * @var Modern_Facebook
     */
    protected $_facebook;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Modern_Facebook
     */
    public function init()
    {
        if (PHP_SAPI !== 'cli') {
            Zend_Session::start();
        }

        return $this->getFacebook();
    }

    /**
     * @return Modern_Facebook
     */
    public function getFacebook()
    {
        if (null === $this->_facebook) {

            $this->_facebook = $this->newInstance();

            // register controller plugin
            Zend_Controller_Front::getInstance()->registerPlugin(
                new Modern_Facebook_Controller_Plugin_Facebook()
            );
        }

        return $this->_facebook;
    }

    /**
     * Get clear instance.
     *
     * @return \Modern_Facebook
     */
    public function newInstance()
    {
        return new Modern_Facebook($this->getOptions());
    }

}
