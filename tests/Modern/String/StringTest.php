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
 * @package     Modern_String
 * @subpackage  UnitTests
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * @category    Modern
 * @package     Modern_String
 * @subpackage  UnitTests
 * @group       Modern_String
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class StringTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $obj = new Modern_String();
        $this->assertEquals('', $obj->getString());

        $obj = new Modern_String('somestring');
        $this->assertEquals('somestring', $obj->getString());

        $string = new ModernTest_String_TestAsset_StringObject('foo');
        $obj = new Modern_String($string);
        $this->assertEquals('foo', $obj->getString());

        $obj = new Modern_String(34534);
        $this->assertEquals('34534', $obj->getString());
    }

    /**
     * Ensures that an exception is thrown when a non-string passed to constructor.
     */
    public function testConstructNotString()
    {
        $this->setExpectedException('Modern_String_Exception', 'Value must be string');
        new Modern_String(array('foo'));
    }

    /**
     * @covers Modern_String::getString
     * @todo Implement testGetString().
     */
    public function testGetString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::random
     * @todo Implement testRandom().
     */
    public function testRandom()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::stricmp
     * @todo Implement testStricmp().
     */
    public function testStricmp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::stripNewlines
     * @todo Implement testStripNewlines().
     */
    public function testStripNewlines()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testToAscii()
    {
        $data = array(
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'          // ASCII
                => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '1234567890' => '1234567890',                                   // Numbers
            '`-=[]\;\',./!@#$%^&*(){}|":<>?'                                // Signs
                => '`-=[]\;\',./!@#$%^&*(){}|":<>?',
            '«»–—“„’' => '<<>>---",,\'',                                    // Special signs
            '$€¢£¥₩₪฿₫₴₹' => '$EUR?????????',                               // Currencies
            'ťúůýáčďéěíňóřšÚŮÝŽÁČĎÉĚÍŇÓŘŠ'                                  // Czech
                => 'tuuyacdeeinorsUUYZACDEEINORS',
            'åæøÅÆÉØ' => 'aae?AAEE?',                                       // Danish
            'éëïóöüÉËÏÓÖÜ' => 'eeioouEEIOOU',                               // Dutch
            'ĉĝĥĵŝŭĈĜĤĴŜŬ' => 'cghjsuCGHJSU',                               // Esperanto
            'äåöÄÅÖ' => 'aaoAAO',                                           // Finnish
            'ùûüÿàâæçéèêëïîôœÙÛÜŸÀÂÆÇÉÈÊËÏÎÔŒ'                              // French
                => 'uuuyaaaeceeeeiiooeUUUYAAAECEEEEIIOOE',
            'äöüßÄÖÜẞ' => 'aoussAOU?',                                      // German
            'áéíöóőüúűÁÉÍÖÓŐÜÚŰ' => 'aeiooouuuAEIOOOUUU',                   // Hungarian
            'áæðéíóöþúýÁÆÐÉÍÓÖÞÚÝ' => 'aae?eioo?uyAAE?EIOO?UY',             // Icelandic
            'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ' => 'acelnoszzACELNOSZZ',                   // Polish
            'ăâîşșţțĂÂÎŞȘŢȚ' => 'aaissttAAISSTT',                           // Romanian
            'ёъяшертыуиопющэдфгчйкльжзцвбнм'
                => '',                                                      // Russian lowercase
            'ЁЪЯШЕРТЫУИОПЮЩЭДФГЧЙКЛЬЖЗЦВБНМ'
                => '', // Russian uppercase
            'çğıİöşüÇĞIİÖŞÜ' => 'cg?IosuCGIIOSU',                           // Turkish
            // ûüúùŵẅẃẁŷÿýỳâäáàêëéèîïíìôöóò
            // ÛÜÚÙŴẄẂẀŶŸÝỲÂÄÁÀÊËÉÈÎÏÍÌÔÖÓÒ
            // Welsh
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $this->assertEquals($expected, (string)$string->toAscii());
        }
    }

    /**
     * @covers Modern_String::lowercase
     * @todo Implement testLowercase().
     */
    public function testLowercase()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::toUrl
     * @todo Implement testToUrl().
     */
    public function testToUrl()
    {
        $data = array(
            'zażółć gęślą jaźń ZAŻÓŁĆ GĘŚLĄ JAŹŃ' => 'zazolc-gesla-jazn-zazolc-gesla-jazn',
            'äöüß ÄÖÜẞ' => 'aouss-aou',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $this->assertEquals($expected, (string)$string->toUrl());
        }
    }

    /**
     * @covers Modern_String::trim
     * @todo Implement testTrim().
     */
    public function testTrim()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::ucfirst
     * @todo Implement testUcfirst().
     */
    public function testUcfirst()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::length
     * @todo Implement testLength().
     */
    public function testLength()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::__toString
     * @todo Implement test__toString().
     */
    public function test__toString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Modern_String::wordTruncate
     * @todo Implement testWordTruncate().
     */
    public function testWordTruncate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}
