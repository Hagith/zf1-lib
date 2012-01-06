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
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_View_Helper_Abstract */
require_once('Zend/View/Helper/Abstract.php');

/**
 * Helper widoku generujący Facebook Like Box.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  View
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */
class Modern_Facebook_View_Helper_LikeBox extends Zend_View_Helper_Abstract
{
    /**
     * Adres API pluginu Like Box.
     *
     * @var string
     */
    protected $_pluginApi = 'http://www.facebook.com/plugins/fan.php';

    /**
     * Domyślne wartości parametrów GET adresu ramki
     * zawierającej wpisy z profilu "wall".
     *
     * @var array
     */
    protected $_defaults = array(
        'colorscheme'   => 'light',
        'header'        => 'false',
        'force_wall'    => 'true',
        'stream'        => 'true',
        'connections'   => '0',
    );

    /**
     * Generuje kod iframe Like Box'a.
     *
     * @param string $fanpageHref
     * @param integer $width
     * @param integer $height
     * @param array $params
     * @param string $css
     * @return string
     */
    public function likeBox($fanpageHref, $width, $height, $params = array(), $css = '')
    {
        $args = array();
        foreach($this->_defaults as $k => $v) {
            $args[$k] = (isset($params[$k])) ? $params[$k] : $v;
        }

        $args['href'] = $fanpageHref;
        $args['css'] = $css;

        $iframeSrc = $this->_pluginApi . '?' . htmlentities(http_build_query($args));

        $script = '<iframe src="%s" scrolling="no" frameborder="0" ' .
            'style="overflow:hidden;width:%spx; height:%spx;" ' .
            'allowTransparency="true"></iframe>';
        return sprintf($script, $iframeSrc, $width, $height);
    }
}