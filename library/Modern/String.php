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
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Class for string manipulations.
 *
 * @category    Modern
 * @package     Modern_String
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_String
{
    /**
     * Current string for operations.
     *
     * @var string
     */
    protected $_string = '';

    /**
	 * Current encoding.
	 *
	 * @var string
	 */
	protected $_encoding = 'utf-8';

    /**
     * @param string $string
     */
    public function __construct($string = null, $encoding = null)
    {
        if (null !== $string) {
            $this->setString($string);
        }

        if (null !== $encoding) {
            $this->setEncoding($encoding);
        }
    }

    /**
     * Set new string for operations.
     *
     * @param string $string
     * @return \Modern_String
     */
    public function setString($string, $encoding = null)
    {
        if (is_object($string) && method_exists($string, '__toString')) {
            $string = (string)$string;
        }

        if (is_numeric($string)) {
            $string = (string)$string;
        }

        if (!is_string($string)) {
            /** @see Modern_String_Exception */
            require_once 'Modern/String/Exception.php';
            throw new Modern_String_Exception('Value must be string');
        }

        if (null !== $encoding) {
            $this->setEncoding($encoding);
        }

        $this->_string = $string;

        return $this;
    }

    /**
     * Get current string.
     *
     * @return string
     */
    public function getString()
    {
        return $this->__toString();
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * @param string $encoding
     * @return \Modern_String
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;

        return $this;
    }

    /**
     * Compare (case insensitive) current string with given.
     *
     * @param string $neddle
     * @param string $encoding
     * @return boolean
     */
    public function stricmp($neddle)
    {
        if (
            0 === mb_stripos($this->_string, $neddle, 0, $this->_encoding)
            && mb_strlen($this->_string, $this->_encoding) === mb_strlen($neddle, $this->_encoding)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Convert text to single line.
     *
     * @return \Modern_String
     */
    public function stripNewlines()
    {
        if (empty($this->_string)) {
            return $this;
        }

        $this->_string = str_replace(array("\r\n", "\n", "\r", "\t"), ' ', $this->_string);
        $this->_string = preg_replace('/[ ]+/', ' ', $this->_string);

        return $this;
    }

    /**
     * Replace non ASCII letters to their equivalents.
     *
     * @return \Modern_String
     */
    public function toAscii()
    {
        if (empty($this->_string)) {
            return $this;
        }

        $this->_string = Modern_String_Transliteration::toAscii($this->_string);

        return $this;
    }

    /**
     * @return \Modern_String
     */
    public function lowercase()
    {
        if (empty($this->_string)) {
            return $this;
        }

        $this->_string = mb_strtolower($this->_string, 'utf-8');

        return $this;
    }

    /**
     * @param boolean $lowercase
     * @return \Modern_String
     */
    public function toUrl($lowercase = true)
    {
        if (empty($this->_string)) {
            return $this;
        }

        $this->toAscii();

        if ($lowercase) {
            $this->lowercase();
        }

        $this->_string = preg_replace('/\W/', '-', $this->_string);
        $this->_string = preg_replace('/[-]+/', '-', $this->_string);
        $this->_string = trim($this->_string, '-');

        return $this;
    }

    /**
     * @param string $charlist
     * @return \Modern_String
     */
    public function trim($charlist = null)
    {
        if (null === $charlist) {
            // null as second argument doesn't working!
            $this->_string = trim($this->_string);
        } else {
            $this->_string = trim($this->_string, $charlist);
        }

        return $this;
    }

    /**
     * @return \Modern_String
     */
    public function ucfirst()
    {
        $length = $this->length();
        if (0 == $length) {
            return $this;
        }

        $first = mb_substr($this->_string, 0, 1, $this->_encoding);
        $first = mb_convert_case($first, MB_CASE_UPPER, $this->_encoding);
        $this->_string = $first . mb_substr($this->_string, 1, $length, $this->_encoding);

        return $this;
    }

    /**
     * @return integer
     */
    public function length()
    {
        return mb_strlen($this->_string, $this->_encoding);
    }

    /**
     * @param string $allowableTags
     * @return \Modern_String
     */
    public function stripTags($allowableTags = null)
    {
        $this->_string = strip_tags($this->_string, $allowableTags);

        return $this;
    }

    /**
     * Truncate string after $lenght respecting full words.
     *
     * @param integer $length
     * @param string $etc
     * @return \Modern_String
     */
    public function wordTruncate($length, $etc = '')
    {
        if ($length <= 0) {
            /** @see Modern_String_Exception */
            require_once 'Modern/String/Exception.php';
            throw new Modern_String_Exception('Length must be greater than zero');
        }

        if (0 == $this->length()) {
            return $this;
        }

        $words = explode(' ', $this->_string);
        $this->_string = '';
        foreach ($words as &$word) {
            if ($this->length() >= $length) {
                $this->trim(' -=\\;,./!@#$%^&*|":?');
                $this->_string .= $etc;
                break;
            }
            $this->_string .= $word . ' ';
        }

        $this->trim();

        return $this;
    }

    /**
     * Cast object to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_string;
    }

    /**
     * Generate random string with specified length,
     * minimal numer of digits using specified set of characters.
     *
     * @param integer $length
     * @param integer $minNumberOfDigits
     * @param string $useChars
     * @return \Modern_String
     */
    public static function random($length = 6, $minNumberOfDigits = 1, $useChars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz23456789')
    {
        if ($minNumberOfDigits > $length) {
            $minNumberOfDigits = $length;
        }

        $string = '';
        for ($i = 0; $i < $length; $i++) {
            mt_srand((double) microtime() * 170305 * $i);
            $string .= $useChars[mt_rand(0, strlen($useChars) - 1)];
        }

        $tmp = array();
        $digitsCount = preg_match_all('/[0-9]/', $string, $tmp);
        if ($digitsCount < $minNumberOfDigits) {
            $tmp = array();
            for ($i = $digitsCount; $i < $minNumberOfDigits; $i++) {
                do {
                    $d_pos = mt_rand(0, $length - 1);
                } while (in_array($d_pos, $tmp) || preg_match('/[0-9]/', $string{$d_pos}));
                array_push($tmp, $d_pos);
                $string{$d_pos} = mt_rand(0, 9);
            }
            unset($tmp);
        }

        // shuffle the order of characters
        srand();
        $string = str_shuffle((string) $string);

        return new self($string);
    }

    /**
     * @param integer $length
     * @return \Modern_String
     */
    public static function randomNumber($length)
    {
        return self::random($length, $length, '0123456789');
    }

    /**
     * @param string $text
     * @param integer $length
     * @param string $etc
     * @return \Modern_String
     */
    public static function excerpt($text, $length, $etc = '')
    {
        $string = new self($text);

        return $string->stripTags('<br>')->wordTruncate($length, $etc);
    }

}
