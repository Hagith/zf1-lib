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
 * Get facebook JS API init code.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  View
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_View_Helper_FacebookInit extends Zend_View_Helper_Abstract
{
    /**
     * Skrypt inicjujący Facebook JS SDK.
     *
     * @param boolean $async
     * @return string
     * @todo obługa locale
     */
    public function facebookInit($async = true)
    {
        $facebook = Modern_Application::getInstance()->getResource('facebook');

        $fbInit = "FB.init({appId:'%s',status:true,cookie:true,xfbml:true});" . PHP_EOL;
        $fbInit .= 'if(window.opera){' . PHP_EOL;
        $fbInit .= 'FB.XD._transport="postmessage";' . PHP_EOL;
        $fbInit .= 'FB.XD.PostMessage.init(); }' . PHP_EOL;
        $fbInit .= "$(document).ready(function() { FB.Canvas.setSize({height: $('body').height()}); });" . PHP_EOL;
        $fbInit = sprintf($fbInit, $facebook->getAppId());
        $script = '';

        $script .= '<div id="fb-root"></div>' . PHP_EOL;
        if (!$async) {
            $script .= '<script src="http://connect.facebook.net/pl_PL/all.js" type="text/javascript"></script>' . PHP_EOL;
        }
        $script .= '<script type="text/javascript">' . PHP_EOL;

        if ($async) {
            $script .= "window.fbAsyncInit = function() { $fbInit  };" . PHP_EOL;
            $script .= "(function() {" . PHP_EOL;
            $script .= "var e = document.createElement('script');" . PHP_EOL;
            $script .= "e.src = document.location.protocol + '//connect.facebook.net/pl_PL/all.js';" . PHP_EOL;
            $script .= "e.async = true;" . PHP_EOL;
            $script .= "document.getElementById('fb-root').appendChild(e);" . PHP_EOL;
            $script .= "}());" . PHP_EOL;
        } else {
            $script .= $fbInit . PHP_EOL;
        }
        $script .= '</script>' . PHP_EOL;

        return $script;
    }

}
