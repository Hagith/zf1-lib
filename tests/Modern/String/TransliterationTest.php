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
class TransliterationTest extends PHPUnit_Framework_TestCase
{
    public static $provider = array(
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'          // ASCII
            => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '1234567890' => '1234567890',                                   // Numbers
        '`-=[]\;\',./!@#$%^&*(){}|":<>?'                                // Signs
            => '`-=[]\;\',./!@#$%^&*(){}|":<>?',
        '«»–—“„’' => '<<>>---",,\'',                                    // Special signs
        '$€¢£¥₩₪฿₫₴₹' => '$EUC/PSY=WNSBh.D??',                          // Currencies
        'ťúůýáčďéěíňóřšÚŮÝŽÁČĎÉĚÍŇÓŘŠ'                                  // Czech
            => 'tuuyacdeeinorsUUYZACDEEINORS',
        'åæøÅÆÉØ' => 'aaeoAAEEO',                                       // Danish
        'éëïóöüÉËÏÓÖÜ' => 'eeioouEEIOOU',                               // Dutch
        'ĉĝĥĵŝŭĈĜĤĴŜŬ' => 'cghjsuCGHJSU',                               // Esperanto
        'äåöÄÅÖ' => 'aaoAAO',                                           // Finnish
        'ùûüÿàâæçéèêëïîôœÙÛÜŸÀÂÆÇÉÈÊËÏÎÔŒ'                              // French
            => 'uuuyaaaeceeeeiiooeUUUYAAAECEEEEIIOOE',
        'äöüßÄÖÜẞ' => 'aoussAOU?',                                      // German
        'áéíöóőüúűÁÉÍÖÓŐÜÚŰ' => 'aeiooouuuAEIOOOUUU',                   // Hungarian
        'áæðéíóöþúýÁÆÐÉÍÓÖÞÚÝ' => 'aaedeioothuyAAEDEIOOThUY',           // Icelandic
        'ąćęłńóśźżĄĆĘŁŃÓŚŹŻ' => 'acelnoszzACELNOSZZ',                   // Polish
        'ăâîşșţțĂÂÎŞȘŢȚ' => 'aaissttAAISSTT',                           // Romanian
        'ёъяшертыуиопющэдфгчйкльжзцвбнм'
            => 'yoyashertyuiopyushchedfgchyklzhzcvbnm',                 // Russian lowercase
        'ЁЪЯШЕРТЫУИОПЮЩЭДФГЧЙКЛЬЖЗЦВБНМ'
            => 'YoYaShERTYUIOPYuShchEDFGChYKLZhZCVBNM',                 // Russian uppercase
        'çğıİöşüÇĞIİÖŞÜ' => 'cgiIosuCGIIOSU',                           // Turkish
        'ûüúùŵẅẃẁŷÿýỳâäáàêëéèîïíìôöóò'
            => 'uuuuwwwwyyyyaaaaeeeeiiiioooo',                          // Welsh lowercase
        'ÛÜÚÙŴẄẂẀŶŸÝỲÂÄÁÀÊËÉÈÎÏÍÌÔÖÓÒ'
            => 'UUUUWWWWYYYYAAAAEEEEIIIIOOOO',                          // Welsh uppercase
        '' => '',
    );

    public function testToAscii()
    {
        foreach (self::$provider as $original => $expected) {
            $this->assertEquals($expected, Modern_String_Transliteration::toAscii($original));
        }

        $languages = array('en', 'pl', 'ru');
        foreach ($languages as $lang) {
            foreach (self::$provider as $original => $expected) {
                $this->assertEquals($expected, Modern_String_Transliteration::toAscii($original, $lang));
            }
        }

        require_once 'Zend/Locale.php';
        $languages = array('en_US', 'pl_PL', 'ru_RU');
        foreach ($languages as $lang) {
            Zend_Registry::set('Zend_Locale', new Zend_Locale($lang));

            foreach (self::$provider as $original => $expected) {
                $this->assertEquals($expected, Modern_String_Transliteration::toAscii($original));
            }
        }

    }

}
