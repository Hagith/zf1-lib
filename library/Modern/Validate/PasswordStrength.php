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
 * @package     Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * PasswordStrength validator.
 *
 * @category    Modern
 * @package     Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Validate_PasswordStrength extends Zend_Validate_Abstract
{
    const NOT_COMPLY = 'passwordNotComply';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_COMPLY   => "Given password does not comply requirements",
    );

    protected $_length;
    protected $_lower = true;
    protected $_upper = true;
    protected $_digitOrSpecial = true;
    protected $_diactric = false;

    protected $_regexParts = array(
        'length' => '(?=.{%d,})',
        'lower' => '(?=.*[a-z])',
        'upper' => '(?=.*[A-Z])',
        'digitOrSpecial' => '(?=.*[\d\W])',
    );

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'length' => scalar, minimum password length
     *   'lower' => boolean, at least 1 lowercase character
     *   'upper' => boolean, at least 1 uppercase character
     *   'digitOrSpecial' => boolean, at least 1 digit or 1 symbol
     *   'diactric' => boolean, allow diactric characters, default to false
     *
     * @param array|Zend_Config $options
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp['length'] = array_shift($options);
            if (!empty($options)) {
                $temp['lower'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['upper'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['digitOrSpecial'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['diactric'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('length', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Missing option. 'length' has to be given");
        }

        foreach ($options as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    public function getLength()
    {
        return $this->_length;
    }

    public function setLength($length)
    {
        if ((int)$length <= 0) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Length must be greater than zero');
        }
        $this->_length = (int)$length;

        return $this;
    }

    public function getLower()
    {
        return $this->_lower;
    }

    public function setLower($lower)
    {
        $this->_lower = (boolean)$lower;

        return $this;
    }

    public function getUpper()
    {
        return $this->_upper;
    }

    public function setUpper($upper)
    {
        $this->_upper = (boolean)$upper;

        return $this;
    }

    public function getDigitOrSpecial()
    {
        return $this->_digitOrSpecial;
    }

    public function setDigitOrSpecial($digitOrSpecial)
    {
        $this->_digitOrSpecial = (boolean)$digitOrSpecial;

        return $this;
    }

    public function getDiactric()
    {
        return $this->_diactric;
    }

    public function setDiactric($diactric)
    {
        $this->_diactric = (boolean)$diactric;

        return $this;
    }

    public function isValid($value)
    {
        $this->_setValue($value);

        $regex = sprintf($this->_regexParts['length'], $this->getLength());

        if ($this->getLower()) {
            $regex .= $this->_regexParts['lower'];
        }

        if ($this->getUpper()) {
            $regex .= $this->_regexParts['lower'];
        }

        if ($this->getDigitOrSpecial()) {
            $regex .= $this->_regexParts['digitOrSpecial'];
        }

        $valid = (bool)preg_match("/^.*$regex.*$/", $value);

        if ($valid && !$this->getDiactric()) {
            $valid = (iconv('utf-8', 'ASCII//IGNORE//TRANSLIT', $value) == $value);
        }

        if (!$valid) {
            $this->_error(self::NOT_COMPLY);
            return false;
        }

        return true;
    }

}
