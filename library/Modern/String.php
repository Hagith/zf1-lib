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
     * @param string $string
     */
    public function __construct($string = null)
    {
        if (null !== $string) {
            $this->setString($string);
        }
    }

    /**
     * Set new string for operations.
     *
     * @param string $string
     * @return Modern_String
     */
    public function setString($string)
    {
        if (!is_string($string)) {
            throw new Modern_String_Exception('Value must be string.');
        }

        $this->_string = $string;

        return $this;
    }

    /**
     * Get current string.
     *
     */
    public function getString()
    {
        return $this->__toString();
    }

    /**
     * Generate random string with specified length,
     * minimal numer of digits using specified set of characters.
     *
     * @param integer $length
     * @param integer $minNumberOfDigits
     * @param string $useChars
     * @return Modern_String
     */
    public static function random($length = 6, $minNumberOfDigits = 1, $useChars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz23456789")
    {
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            mt_srand((double) microtime() * 170305 * $i);
            $string .= $useChars[mt_rand(0, strlen($useChars) - 1)];
        }

        if (!preg_match("/[0-9]/", $string)) {
            $tmp = array();
            for ($i = 0; $i <= $minNumberOfDigits && $i < $length; $i++) {
                do {
                    $d_pos = mt_rand(0, $length - 1);
                } while (in_array($d_pos, $tmp));
                array_push($tmp, $d_pos);
                $string[$d_pos] = mt_rand(0, 9);
            }
        }

        // shuffle the order of characters
        srand();
        $string = str_shuffle((string) $string);

        return new self($string);
    }

    /**
     * Compare (case insensitive) current string with given.
     *
     * @param string $neddle
     * @param string $encoding
     * @return boolean
     */
    public function stricmp($neddle, $encoding = 'utf-8')
    {
        if (
            0 === mb_stripos($this->_string, $neddle, 0, $encoding)
            && mb_strlen($this->_string, $encoding) === mb_strlen($neddle, $encoding)
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

        $this->_string = str_replace("\r\n", " ", $this->_string);
        $this->_string = str_replace("\n", " ", $this->_string);
        $this->_string = str_replace("\r", " ", $this->_string);
        $this->_string = str_replace("\t", "", $this->_string);
        $this->_string = preg_replace('#[ ]+#', ' ', $this->_string);

        return $this;
    }

    /**
     * Replace non ASCII letters to their equivalents.
     *
     * @return Modern_String
     */
    public function toAscii()
    {
        if (empty($this->_string)) {
            return $this;
        }

        $this->_string = iconv("utf-8", "ASCII//IGNORE//TRANSLIT", $this->_string);

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
            $this->_string = mb_strtolower($this->_string, 'utf-8');
        }

        $replacements = array(
            '?' => '', "'" => '', '"' => '', '/' => '', '+' => '', ',' => '-',
            '(' => '', ')' => '', ' ' => '-', '&' => '', ':' => '-', '!' => '',
        );
        $this->_string = strtr($this->_string, $replacements);
        $this->_string = preg_replace('|[-]+|', '-', $this->_string);
        $this->_string = urlencode($this->_string);

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
        if (0 == $this->length()) {
            return $this;
        }

        $first = mb_substr($this->_string, 0, 1, $this->_encoding);
        $first = mb_convert_case($first, MB_CASE_UPPER, $this->_encoding);
        $this->_string = $first . mb_substr($this->_string, 1, $strlen, $this->_encoding);

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
     * Cast object to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_string;
    }

    /**
     * Truncate string after $lenght respecting full words.
     *
     * @param string $string
     * @param integer $length
     * @param string $etc
     * @return Modern_String
     */
    public static function wordTruncate($string, $length, $etc = '')
    {
        $string = strip_tags($string);
        $words = explode(" ", $string);
        if (is_array($words)) {
            $string = '';
            foreach ($words as &$word) {
                if (mb_strlen($string) > $length) {
                    $string = trim($string) . $etc;
                    break;
                }
                $string .= $word . ' ';
            }
        }

        return new self(trim($string));
    }

}
