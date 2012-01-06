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
 * @package     Modern_Application
 * @subpackage  Website
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Bootstrap aplikacji website.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Website
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
require_once((is_readable('../configs/env.php'))
    ? '../configs/env.php'
    : '../configs/env.php.dist'
);

/** @see Modern_Application */
require_once('Modern/Application.php');

$application = new Modern_Application(
    ENVIRONMENT,
    ROOT_PATH . "/configs/application.ini",
    'website'
);
$application->bootstrap()->run();