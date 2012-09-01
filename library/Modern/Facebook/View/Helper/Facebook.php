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

/** @see Zend_View_Helper_HeadScript */
require_once 'Zend/View/Helper/HeadScript.php';

/**
 * Get facebook JS API init code.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  View
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_View_Helper_FacebookInit extends Zend_View_Helper_HeadScript
{
    /**
     * @var Modern_Application_Resource_Facebook
     */
    protected $_resource;

    /**
     * @return Modern_Facebook_View_Helper_Facebook
     */
    public function facebook()
    {
        $this->_resource = Modern_Application::getInstance()->getResource('facebook');

        if (!$this->_resource) {
            throw new Modern_Facebook_Exception('Facebook application resource not initialized');
        }

        return $this;
//        $fbInit = "FB.init({appId:'%s',status:true,cookie:true,xfbml:true});" . PHP_EOL;
//        $fbInit .= 'if(window.opera){' . PHP_EOL;
//        $fbInit .= 'FB.XD._transport="postmessage";' . PHP_EOL;
//        $fbInit .= 'FB.XD.PostMessage.init(); }' . PHP_EOL;
//        $fbInit .= "$(document).ready(function() { FB.Canvas.setSize({height: $('body').height()}); });" . PHP_EOL;
//        $fbInit = sprintf($fbInit, $facebook->getAppId());
//        $script = '';
//
//        $script .= '<div id="fb-root"></div>' . PHP_EOL;
//        if (!$async) {
//            $script .= '<script src="http://connect.facebook.net/pl_PL/all.js" type="text/javascript"></script>' . PHP_EOL;
//        }
//        $script .= '<script type="text/javascript">' . PHP_EOL;
//
//        if ($async) {
//            $script .= "window.fbAsyncInit = function() { $fbInit  };" . PHP_EOL;
//            $script .= "(function() {" . PHP_EOL;
//            $script .= "var e = document.createElement('script');" . PHP_EOL;
//            $script .= "e.src = document.location.protocol + '//connect.facebook.net/pl_PL/all.js';" . PHP_EOL;
//            $script .= "e.async = true;" . PHP_EOL;
//            $script .= "document.getElementById('fb-root').appendChild(e);" . PHP_EOL;
//            $script .= "}());" . PHP_EOL;
//        } else {
//            $script .= $fbInit . PHP_EOL;
//        }
//        $script .= '</script>' . PHP_EOL;
//
//        return $script;
    }

    /**
     * @param string $indent
     * @todo handle application locale
     */
    public function toString($indent = null)
    {
        $indent = (null !== $indent)
            ? $this->getWhitespace($indent)
            : $this->getIndent();

        $params = array(
            'appId' => $this->_resource->getAppId(),
            'channelUrl' => Zend_Controller_Front::getInstance()->getRequest()->getHttpHost() . '/channel.html',
        );

        var_dump($params);
        exit;

//<div id="fb-root"></div>
//<script>
//  window.fbAsyncInit = function() {
//    FB.init({
//      appId      : 'YOUR_APP_ID', // App ID
//      channelUrl : '//WWW.YOUR_DOMAIN.COM/channel.html', // Channel File
//      status     : true, // check login status
//      cookie     : true, // enable cookies to allow the server to access the session
//      xfbml      : true  // parse XFBML
//    });
//
//    // Additional initialization code here
//  };
//
//  // Load the SDK Asynchronously
//  (function(d){
//     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
//     if (d.getElementById(id)) {return;}
//     js = d.createElement('script'); js.id = id; js.async = true;
//     js.src = "//connect.facebook.net/en_US/all.js";
//     ref.parentNode.insertBefore(js, ref);
//   }(document));
//</script>
    }

}
