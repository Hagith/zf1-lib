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
 * @package     Modern_Mime
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Mime */
require_once('Zend/Mime.php');

/**
 * Klasa utworzona, aby "nadpisać" wartość stałej Zend_Mime::LINELENGTH
 *
 * @category    Modern
 * @package     Modern_Mime
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Mime extends Zend_Mime
{
    const LINELENGTH = 76;

    /**
     * Encode the given string with the given encoding.
     *
     * @param string $str
     * @param string $encoding
     * @param string $EOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     * @return string
     */
    public static function encode($str, $encoding, $EOL = self::LINEEND)
    {
        switch ($encoding) {
            case self::ENCODING_BASE64:
                return self::encodeBase64($str, self::LINELENGTH, $EOL);

            case self::ENCODING_QUOTEDPRINTABLE:
                return self::encodeQuotedPrintable($str, self::LINELENGTH, $EOL);

            default:
                /**
                 * @todo 7Bit and 8Bit is currently handled the same way.
                 */
                return $str;
        }
    }

}
