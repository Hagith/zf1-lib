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
 * @package     Modern_Log
 * @subpackage  Formatter
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Log_Formatter_Abstract */
require_once 'Zend/Log/Formatter/Abstract.php';

/**
 * @category    Modern
 * @package     Modern_Log
 * @subpackage  Formatter
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Log_Formatter_Firebug extends Zend_Log_Formatter_Abstract
{
    /**
     * Factory for Zend_Log_Formatter_Firebug classe
     *
     * @param array|Zend_Config $options useless
     * @return Zend_Log_Formatter_Firebug
     */
    public static function factory($options)
    {
        return new self;
    }

    /**
     * This method formats the event for the firebug writer.
     *
     * @param  array    $event    event data
     * @return mixed              event message
     */
    public function format($event)
    {
        if (is_string($event['message'])) {
            $event['message'] = sprintf("%s @ %s(%s)", $event['message'], $event['file'], $event['line']);
        }

        return $event['message'];
    }

}
