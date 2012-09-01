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
 * @subpackage  View
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_View_Helper_Abstract */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Adds Open Graph tags.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  View
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_View_Helper_OpenGraph extends Zend_View_Helper_Abstract
{
    /**
     * meta'y dla OG.
     *
     * @var <type>
     */
    private $_html = '';

    /**
     * Zwraca obiekt helpera.
     *
     * @return Modern_Facebook_View_Helper_OpenGraph
     */
    public function openGraph()
    {
        return $this;
    }

    /**
     * Konfigurje helper.
     *
     * @param array $options
     * @return Modern_Facebook_View_Helper_OpenGraph
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option) {
            if (empty($option['property']) || empty($option['content'])) {
                $e = new Zend_View_Exception('Każda z opcji konfiguracyjnych musi posiadać właściwości property oraz content');
                $e->setView($this->view);
                throw $e;
            }
            $this->_html .= "<meta property=\"{$option['property']}\" content=\"{$option['content']}\" />\n";
        }
        return $this;
    }

    /**
     * Generuje elementy <meta /> dla OG.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_html;
    }

}
