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

        $text = iconv('utf-8', 'iso-8859-2', 'zażółć gęślą jaźń');
        $obj = new Modern_String($text, 'iso-8859-2');
        $this->assertEquals($text, $obj->getString());
    }

    /**
     * Ensures that an exception is thrown when a non-string passed to constructor.
     */
    public function testConstructNotString()
    {
        $this->setExpectedException('Modern_String_Exception', 'Value must be string');
        new Modern_String(array('foo'));
    }

    public function testSetString()
    {
        $obj = new Modern_String('somestring');
        $obj->setString('otherstring');
        $this->assertEquals('otherstring', $obj->getString());

        $string = new ModernTest_String_TestAsset_StringObject('foo');
        $obj->setString($string);
        $this->assertEquals('foo', $obj->getString());

        $text = iconv('utf-8', 'iso-8859-2', 'zażółć gęślą jaźń');
        $obj->setString($text, 'iso-8859-2');
        $this->assertEquals($text, $obj->getString());
    }

    public function testSetEncoding()
    {
        $encodings = array(
            'iso-8859-1',
            'iso-8859-2',
            'windows-1250',
            'windows-1251',
            'utf-8',
            'utf-16',
        );

        foreach ($encodings as $encoding) {
            $string = new Modern_String();
            $string->setEncoding($encoding);
            $this->assertEquals($encoding, $string->getEncoding());
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testRandom()
    {
        $random = Modern_String::random()->getString();
        $this->assertEquals(6, strlen($random));

        $lengths = array(2, 6, 10, 20, 50);
        foreach ($lengths as $length) {
            $random = Modern_String::random($length)->getString();
            $this->assertEquals($length, strlen($random));
            $this->assertGreaterThanOrEqual(1, preg_match_all('/[0-9]/', $random, $m));

            // test $minNumberOfDigits
            $random = Modern_String::random(100, $length)->getString();
            $this->assertGreaterThanOrEqual($length, preg_match_all('/[0-9]/', $random, $m));

            $random = Modern_String::random($length, $length + 10)->getString();
            $this->assertGreaterThanOrEqual($length, preg_match_all('/[0-9]/', $random, $m));

            // test $useChars
            $random = Modern_String::random($length, 0, 'a')->getString();
            $this->assertEquals(str_repeat('a', $length), $random);

            $random = Modern_String::random($length, 0, 'abcdefghijklmnopqrstuvwxyz')->getString();
            $this->assertEquals(preg_match_all('/[a-z]/', $random, $m), strlen($random));

            $random = Modern_String::random($length, 0, '0123456789')->getString();
            $this->assertEquals(preg_match_all('/[0-9]/', $random, $m), strlen($random));
        }
    }

    public function testRandomNumber()
    {
        $lengths = array(2, 6, 10, 20, 50);
        foreach ($lengths as $length) {
            $number = Modern_String::randomNumber($length)->getString();
            $this->assertEquals(preg_match_all('/[0-9]/', $number, $m), strlen($number));
        }
    }

    public function testStricmp()
    {
        $equal = array(
            'ZażóŁć Gęślą jaŹŃ' => 'zażółĆ GęślĄ Jaźń',
            'abcdefghijklmnopqrstuvwxyz0123456789' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            '123123' => '123123',
        );

        foreach ($equal as $string1 => $string2) {
            $string = new Modern_String($string1);
            $this->assertTrue($string->stricmp($string2));

            $string = new Modern_String($string2);
            $this->assertTrue($string->stricmp($string1));
        }

        $notEqual = array(
            'ZażóŁć Gęślą jaŹŃ' => 'zażółĆ GęślĄ Jaźn',
            'abcdefghijklmnopqrstuvwxyz0123456789' => 'ABCDEFGHIJKLMNOPQQSTUVWXYZ0123456789',
            '123' => '123123',
            '123123' => '123'
        );

        foreach ($notEqual as $string1 => $string2) {
            $string = new Modern_String($string1);
            $this->assertFalse($string->stricmp($string2));

            $string = new Modern_String($string2);
            $this->assertFalse($string->stricmp($string1));
        }
    }

    public function testStripNewlines()
    {
        $data = array(
            "some\nmulti\r\nline" => 'some multi line',
            "some\n\n\nmulti\r\nline" => 'some multi line',
            "some
            multi
            line" => 'some multi line',
            "text with \n\t\ttabs" => 'text with tabs',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->stripNewlines();
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testToAscii()
    {
        foreach (TransliterationTest::$provider as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->toAscii();
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testLowercase()
    {
        $data = array(
            'Some TeXt' => 'some text',
            'some TEXT' => 'some text',
            'UPPER' => 'upper',
            'lower' => 'lower',
            'ZAŻÓŁĆ GĘŚLĄ JAŹŃ' => 'zażółć gęślą jaźń',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->lowercase();
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testToUrl()
    {
        $data = array(
            'zażółć gęślą jaźń ZAŻÓŁĆ GĘŚLĄ JAŹŃ' => 'zazolc-gesla-jazn-zazolc-gesla-jazn',
            'äöüß ÄÖÜẞ' => 'aouss-aou',
            '`-=[]\;\',./!@#$%^&*(){}|":<>?' => '',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->toUrl();
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testTrim()
    {
        $data = array(
            'foo  ' => 'foo',
            ' bar' => 'bar',
            ' baz  ' => 'baz',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->trim();
            $this->assertEquals($expected, (string)$string);
        }

        $data = array(
            'foo-' => 'foo',
            '-bar' => 'bar',
            '-baz--' => 'baz',
            '/-baz--/' => 'baz',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->trim('-/');
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testUcfirst()
    {
        $data = array(
            'foo  ' => 'Foo  ',
            ' bar' => ' bar',
            'Baz  ' => 'Baz  ',
            'żółw' => 'Żółw',
            'ósemka' => 'Ósemka',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->ucfirst();
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    public function testLength()
    {
        $data = array(
            'Some TeXt' => 9,
            'UPPER lower' => 11,
            "text\nwith\ttab and \r\n new lines" => 30,
            'ZAŻÓŁĆ GĘŚLĄ JAŹŃ' => 17,
            'Zażółć gęślą - jaźń, zażółć gęślą jaźń.' => 39,
            '' => 0,
        );

        foreach ($data as $text => $length) {
            $string = new Modern_String($text);
            $this->assertEquals($length, $string->length());
        }
    }

    public function testStripTags()
    {
        $data = array(
            '<p>foo</p>' => 'foo',
            '<p>bar<script>alert("baz");</script></p>' => 'baralert("baz");',
            '<strong>some<br/>text<br></strong>' => 'sometext',
            '<span>some<br/>text<br></p>' => 'sometext',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->stripTags();
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }

        // allowedTags = <br>
        $data = array(
            '<p>foo</p>' => 'foo',
            '<p>bar<script>alert("baz");</script></p>' => 'baralert("baz");',
            '<strong>some<br/>text<br></strong>' => 'some<br/>text<br>',
            '<span>some<br/>text<br></p>' => 'some<br/>text<br>',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->stripTags('<br>');
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }

        // allowedTags = <p>
        $data = array(
            '<p>foo</p>' => '<p>foo</p>',
            '<p>bar<script>alert("baz");</script></p>' => '<p>baralert("baz");</p>',
            '<strong>some<br/>text<br></strong>' => 'sometext',
            '<span>some<br/>text<br></p>' => 'sometext</p>',
            '' => '',
        );

        foreach ($data as $original => $expected) {
            $string = new Modern_String($original);
            $string = $string->stripTags('<p>');
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }
    }

    /**
     * @covers Modern_String::wordTruncate
     */
    public function testWordTruncate()
    {
        // length = 60
        $text = 'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.';
        $data = array(
            // length => expected
            1 => 'Lorem',
            6 => 'Lorem',
            7 => 'Lorem ipsum',
            16 => 'Lorem ipsum dolor',
            27 => 'Lorem ipsum dolor (sit amet)',
            28 => 'Lorem ipsum dolor (sit amet)',
            29 => 'Lorem ipsum dolor (sit amet)',
            30 => 'Lorem ipsum dolor (sit amet)',
            41 => 'Lorem ipsum dolor (sit amet), consectetur',
            42 => 'Lorem ipsum dolor (sit amet), consectetur',
            43 => 'Lorem ipsum dolor (sit amet), consectetur',
            44 => 'Lorem ipsum dolor (sit amet), consectetur',
            45 => 'Lorem ipsum dolor (sit amet), consectetur - adipiscing',
            56 => 'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.',
            59 => 'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.',
            60 => 'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.',
            70 => 'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.',
        );

        foreach ($data as $length => $expected) {
            $string = new Modern_String($text);
            $string = $string->wordTruncate($length);
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }

        // length = 39
        $text = 'Zażółć gęślą - jaźń, zażółć gęślą jaźń.';
        $data = array(
            // length => expected
            1 => 'Zażółć',
            6 => 'Zażółć',
            7 => 'Zażółć',
            12 => 'Zażółć gęślą',
            13 => 'Zażółć gęślą',
            15 => 'Zażółć gęślą',
            18 => 'Zażółć gęślą - jaźń',
            20 => 'Zażółć gęślą - jaźń',
            23 => 'Zażółć gęślą - jaźń, zażółć',
            38 => 'Zażółć gęślą - jaźń, zażółć gęślą jaźń.',
            39 => 'Zażółć gęślą - jaźń, zażółć gęślą jaźń.',
        );

        foreach ($data as $length => $expected) {
            $string = new Modern_String($text);
            $string = $string->wordTruncate($length);
            $this->assertEquals($expected, (string)$string);
            $this->assertInstanceOf('Modern_String', $string);
        }

        $string = new Modern_String();
        $string = $string->wordTruncate(1);
        $this->assertEquals('', (string)$string);
        $this->assertInstanceOf('Modern_String', $string);
    }

    /**
     * Ensures that an exception is thrown when a invalid length is provided to Modern_String::wordTruncate().
     */
    public function testWordTruncateWrongLength()
    {
        $this->setExpectedException('Modern_String_Exception', 'Length must be greater than zero');
        $string = new Modern_String('foo bar baz');
        $string->wordTruncate(0);
    }

    public function testExcerpt()
    {
        $text = 'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.' .
            'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.' .
            'Lorem ipsum dolor (sit amet), consectetur - adipiscing elit.';

        $excerpt = Modern_String::excerpt($text, 100);
        $this->assertEquals(101, $excerpt->length());
        $this->assertInstanceOf('Modern_String', $excerpt);

        $excerpt = Modern_String::excerpt($text, 100, '...');
        $this->assertEquals(104, $excerpt->length());
        $this->assertInstanceOf('Modern_String', $excerpt);

        $text = 'Zażółć gęślą - jaźń, zażółć gęślą jaźń.' .
            'Zażółć gęślą - jaźń, zażółć gęślą jaźń.' .
            'Zażółć gęślą - jaźń, zażółć gęślą jaźń.';

        $excerpt = Modern_String::excerpt($text, 41);
        $this->assertEquals(45, $excerpt->length());
        $this->assertInstanceOf('Modern_String', $excerpt);

        $excerpt = Modern_String::excerpt($text, 41, '...');
        $this->assertEquals(48, $excerpt->length());
        $this->assertInstanceOf('Modern_String', $excerpt);
    }

}
