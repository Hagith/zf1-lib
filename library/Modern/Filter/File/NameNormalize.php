<?php
/**
 * Modern
 *
 * LICENSE
 *
 * This source file is subject to version 1.0
 * of the ModernWeb license.
 *
 * @category    Modern
 * @package     Modern_Filter
 * @subpackage  File
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/** @see Modern_Filter_StringNormalize */
require_once 'Modern/Filter/StringNormalize.php';

/**
 * @category    Modern
 * @package     Modern_Filter
 * @subpackage  File
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filter_File_NameNormalize extends Modern_Filter_StringNormalize
{
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