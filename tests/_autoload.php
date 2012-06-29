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
 * @package     Modern
 * @subpackage  UnitTests
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Setup autoloading
 */
function ModernTest_Autoloader($class)
{
    $class = ltrim($class, '\\');

    if (!preg_match('#^(Modern(Test)?|PHPUnit)(\\\\|_)#', $class)) {
        return false;
    }

    $segments = preg_split('#[\\\\_]#', $class); // preg_split('#\\\\|_#', $class);//
    $ns = array_shift($segments);

    switch ($ns) {
        case 'Modern':
            $file = dirname(__DIR__) . '/library/Modern/';
            break;
        case 'ModernTest':
            // temporary fix for ZendTest namespace until we can migrate files
            // into ZendTest dir
            $file = __DIR__ . '/Modern/';
            break;
        default:
            $file = false;
            break;
    }

    if ($file) {
        $file .= implode('/', $segments) . '.php';
        if (file_exists($file)) {
            return include_once $file;
        }
    }

    $segments = explode('_', $class);
    $ns = array_shift($segments);

    switch ($ns) {
        case 'Modern':
            $file = dirname(__DIR__) . '/library/Modern/';
            break;
        default:
            return false;
    }
    $file .= implode('/', $segments) . '.php';
    if (file_exists($file)) {
        return include_once $file;
    }

    return false;
}

spl_autoload_register('ModernTest_Autoloader', true, true);
