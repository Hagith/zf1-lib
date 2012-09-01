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
 * @subpackage  Controller
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Controller
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Controller_Plugin_Facebook extends Zend_Controller_Plugin_Abstract
{
    /**
     * Handle request to channel.html file.
     *
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @todo 304 Not Modified
     * @todo handle application locale
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if (false !== strpos($request->getPathInfo(), 'channel.html')) {
            $cacheExpire = 60 * 60 * 24 * 365;
            header('Pragma: public');
            header('Cache-Control: max-age=' . $cacheExpire);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheExpire) . ' GMT');
            echo '<script src="//connect.facebook.net/pl_PL/all.js"></script>';
            exit;
        }
    }

}
