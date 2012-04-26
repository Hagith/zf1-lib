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

/** @see Zend_Application_Resource_Log */
require_once 'Zend/Application/Resource/Log.php';

/** @see Modern_Log */
require_once 'Modern/Log.php';

/**
 * Extended resource registers error_handler.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_Log extends Zend_Application_Resource_Log
{
    /**
     * @return Modern_Log
     */
    public function getLog()
    {
        if (null === $this->_log) {
            $options = $this->getOptions();
            $log = Modern_Log::factory($options);
            $log->registerErrorHandler();
            $this->setLog($log);
        }

        return $this->_log;
    }

}
