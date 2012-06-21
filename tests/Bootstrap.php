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
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting(E_ALL | E_STRICT);

$phpUnitVersion = PHPUnit_Runner_Version::id();
if ('@package_version@' !== $phpUnitVersion && version_compare($phpUnitVersion, '3.5.0', '<')) {
    echo 'This version of PHPUnit (' . PHPUnit_Runner_Version::id() . ') is not supported in Zend Framework unit tests.' . PHP_EOL;
    exit(1);
}
unset($phpUnitVersion);

/**
 * Determine the root, library, and tests directories of the framework.
 */
$zfRoot = realpath(dirname(__DIR__));
$zfCoreLibrary = "$zfRoot/library";
$zfCoreTests = "$zfRoot/tests";

/**
 * Prepend the Zend Framework library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array(
    $zfCoreLibrary,
    $zfCoreTests,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Setup autoloading
 */
include __DIR__ . '/_autoload.php';

/**
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true) {
    $codeCoverageFilter = PHP_CodeCoverage_Filter::getInstance();

    $lastArg = end($_SERVER['argv']);
    if (is_dir($zfCoreTests . '/' . $lastArg)) {
        $codeCoverageFilter->addDirectoryToWhitelist($zfCoreLibrary . '/' . $lastArg);
    } else if (is_file($zfCoreTests . '/' . $lastArg)) {
        $codeCoverageFilter->addDirectoryToWhitelist(dirname($zfCoreLibrary . '/' . $lastArg));
    } else {
        $codeCoverageFilter->addDirectoryToWhitelist($zfCoreLibrary);
    }

    /**
     * Omit from code coverage reports the contents of the tests directory
     */
    $codeCoverageFilter->addDirectoryToBlacklist($zfCoreTests, '');
    $codeCoverageFilter->addDirectoryToBlacklist(PEAR_INSTALL_DIR, '');
    $codeCoverageFilter->addDirectoryToBlacklist(PHP_LIBDIR, '');

    unset($codeCoverageFilter);
}


/**
 * Start output buffering, if enabled
 */
if (defined('TESTS_OB_ENABLED') && constant('TESTS_OB_ENABLED')) {
    ob_start();
}

/**
 * Unset global variables that are no longer needed.
 */
unset($zfRoot, $zfCoreLibrary, $zfCoreTests, $path);
