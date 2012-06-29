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
 * @subpackage  File
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Modern_Filter_UrlFriendly */
require_once 'Modern/Filter/UrlFriendly.php';

/**
 * @category    Modern
 * @package     Modern_Filter
 * @subpackage  File
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filter_File_NameNormalize extends Modern_Filter_UrlFriendly
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Does a name normalization on the name of the given file.
     *
     * @param string $value Full path of file to change
     * @return string Normalized path
     * @throws Zend_Filter_Exception
     */
    public function filter($value)
    {
        $pathinfo = pathinfo($value);

        $filename = parent::filter($pathinfo['filename']);
        $filename .= '.' . parent::filter($pathinfo['extension']);
        $target = $pathinfo['dirname'] . '/' . $filename;

        $result = rename($value, $target);

        if ($result === true) {
            return $target;
        }

        require_once 'Zend/Filter/Exception.php';
        throw new Zend_Filter_Exception(sprintf("File '%s' could not be renamed. An error occured while processing the file.", $value));
    }

}
