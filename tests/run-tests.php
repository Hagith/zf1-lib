#!/usr/bin/env php
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
 * runtests.php - Launch PHPUnit for specific test group(s).
 *
 * Usage: runtests.sh [ -h <html-dir> ] [ -c <clover-xml-file> ] [ -g ]
 *     [ ALL | <test-group> [ <test-group> ... ] ]
 *
 * This script makes it easier to execute PHPUnit test runs from the
 * shell, using the path to the test or using @group tags defined in
 * the test suite files to run subsets of tests.
 *
 * To get a list of all @group tags: phpunit --list-groups Zend/
 *
 * @category    Modern
 * @package     Modern
 * @subpackage  UnitTests
 */

// PHPUnit doesn't understand relative paths well when they are in the config file.
chdir(__DIR__);

$phpunit_bin      = 'phpunit';
$phpunit_conf     = (file_exists('phpunit.xml') ? 'phpunit.xml' : 'phpunit.xml.dist');
$phpunit_opts     = "-c $phpunit_conf";
$phpunit_coverage = '';

$run_as     = 'paths';
$components = array();

if ($argc == 1) {
    $components = getAll($phpunit_conf);
} else {
    for ($i = 1; $i < $argc; $i++) {
        $arg = $argv[$i];
        switch ($arg) {
            case '-h':
            case '--html':
                $phpunit_coverage = '--coverage-html ' . $argv[++$i];
                break;
            case '-c':
            case '--clover':
                $phpunit_coverage = '--coverage-clover ' . $argv[++$i];
                break;
            case '-g':
            case '--groups':
                $run_as = 'groups';
                break;
            case 'all':
                if ($run_as == 'paths') {
                    $components = getAll($phpunit_conf);
                }
                break;
            default:
                if (strpos($arg, 'Modern') !== false) {
                    $components[] = $arg;
                } else {
                    $components[] = 'Modern_' . $arg;
                }
        }
    }
}

$result = 0;
if ($run_as == 'groups') {
    $groups = join(',', $components);
    echo "$groups:\n";
    system("$phpunit_bin $phpunit_opts $phpunit_coverage --group " . $groups, $result);
    echo "\n\n";
} else {
    foreach ($components as $component) {
        $component =   'Modern/' . basename(str_replace('_', '/', $component));
        echo "$component:\n";
        system("$phpunit_bin $phpunit_opts $phpunit_coverage " . __DIR__ . '/' . $component, $c_result);
        echo "\n\n";
        if ($c_result) {
            $result = $c_result;
        }
    }
}

exit($result);

// Functions
function getAll($phpunit_conf) {
    $components = array();
    $conf = simplexml_load_file($phpunit_conf);
    $excludes = $conf->xpath('/phpunit/testsuites/testsuite/exclude/text()');
    for($i = 0; $i < count($excludes); $i++) {
        $excludes[$i] = basename($excludes[$i]);
    }
    if ($handle = opendir(__DIR__ . '/Modern/')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..' && !in_array($entry, $excludes)) {
                $components[] = $entry;
            }
        }
        closedir($handle);
    }
    sort($components);
    return $components;
}
