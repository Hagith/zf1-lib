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
 * @subpackage  Part
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Mime_Part */
require_once('Zend/Mime/Part.php');

/** @see Modern_Mime */
require_once('Modern/Mime.php');

/**
 * Klasa utworzona, aby nadpisać wartość stałej Zend_Mime::LINELENGTH.
 *
 * @category    Modern
 * @package     Modern_Mime
 * @subpackage  Part
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Mime_Part extends Zend_Mime_Part
{
    /**
     * Zwraca zawartość bieżącej części typu MIME w podanym kodowaniu.
     *
     * @return string
     */
    public function getContent($EOL = Zend_Mime::LINEEND)
    {
        if ($this->_isStream) {
            return stream_get_contents($this->getEncodedStream());
        } else {
            return Modern_Mime::encode($this->_content, $this->encoding, $EOL);
        }
    }

}
