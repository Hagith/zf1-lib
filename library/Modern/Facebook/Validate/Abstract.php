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
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
abstract class Modern_Facebook_Validate_Abstract extends Zend_Validate_Abstract
{
    /**
     *
     * @var Modern_Facebook
     */
    protected $_facebook;

    /**
     *
     * @return Modern_Facebook
     */
    public function getFacebook()
    {
        if (!$this->_facebook) {
            $this->_facebook = Modern_Application::getInstance()->getResource('facebook');
        }
        return $this->_facebook;
    }

}
