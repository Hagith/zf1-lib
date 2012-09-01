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
 * Ensure user is in application canvas.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  View
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_View_Helper_FacebookRedirect extends Zend_View_Helper_Abstract
{

    public function facebookRedirect()
    {
        $application = Modern_Application::getInstance();

        $facebook = $application->getResource('facebook');
        $script = '<script type="text/javascript">' . PHP_EOL;
        $script .= "if(top == self) {" . PHP_EOL;
        $script .= "var pathElements = (top.location.href+'').split('/')" . PHP_EOL;
        $script .= "var query = '';" . PHP_EOL;
        $script .= "for(i = 0; i < pathElements.length; i++) {" . PHP_EOL;
        $script .= " if(i > 2) query += '/' + pathElements[i];" . PHP_EOL;
        $script .= " }" . PHP_EOL;
        if ($application->getEnvironment() != 'development') {
            $script .= "top.location.href = '" . $facebook->getCanvasUrl() . "' + query;" . PHP_EOL;
        }
        $script .= '  };' . PHP_EOL;
        $script .= '</script>' . PHP_EOL;

        return $script;
    }

}
