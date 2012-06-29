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
 * @package     Modern_Filter
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Filter_Interface */
require_once 'Zend/Filter/Interface.php';

/**
 * @category    Modern
 * @package     Modern_Filter
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filter_Youtube implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the YouTube movie ID from $value.
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $matches = array();
        preg_match('/[\/\?]v[=\/]([\w-]+)/', $value, $matches);
        return (isset($matches[1]) && !empty($matches[1])) ? $matches[1] : $value;
    }

}
