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
 * @package     Modern_Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Array count validator.
 *
 * @category    Modern
 * @package     Modern_Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Validate_ArrayCount extends Zend_Validate_Abstract
{
    /**
     * Error codes
     *
     * @const string
     */
    const TOO_FEW  = 'arrayCountTooFew';
    const TOO_MANY = 'arrayCountTooMany';
    const WRONG_KEY = 'wrongKey';
    const NOT_ARRAY = 'notArray';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::TOO_FEW  => "Too few items, minimum '%min%' are expected but '%count%' are given",
        self::TOO_MANY => "Too many items, maximum '%max%' are allowed but '%count%' are given",
        self::WRONG_KEY => "Element indicated by the key '%key%' not found",
        self::NOT_ARRAY => "Element indicated by the key '%key%' is not an array",
    );

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'key'   => '_key',
        'min'   => '_min',
        'max'   => '_max',
        'count' => '_count',
    );

    /**
     * Context key against which to validate.
     *
     * @var string
     */
    protected $_key;

    /**
     * Mminimum array count.
     *
     * @var mixed
     */
    protected $_min;

    /**
     * Maximum array count.
     *
     * @var mixed
     */
    protected $_max;

    /**
     * Array count.
     *
     * @var mixed
     */
    protected $_count;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'key' => string, context key against which to validate
     *   'min' => scalar, minimum array count
     *   'max' => scalar, maximum array count
     *
     * @param array|Zend_Config $options
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp['key'] = array_shift($options);
            if (!empty($options)) {
                $temp['min'] = array_shift($options);
            }
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('key', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Missing option. 'key' has to be given");
        }

        if (!array_key_exists('min', $options) && !array_key_exists('max', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Missing option. 'min' or 'max' has to be given");
        }

        foreach ($options as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * Retrieve context key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Set context key against which to compare
     *
     * @param mixed $key
     * @return \Modern_Validate_ArrayCount
     */
    public function setKey($key)
    {
        $this->_key = (string)$key;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMin()
    {
        return $this->_min;
    }

    /**
     * @param integer $min
     * @return \Modern_Validate_ArrayCount
     * @throws Zend_Validate_Exception
     */
    public function setMin($min)
    {
        if (null !== $this->getMax() && $min >= $this->getMax()) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("'min' must be less than 'max'");
        }
        $this->_min = (int)$min;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMax()
    {
        return $this->_max;
    }

    /**
     * @param integer $max
     * @return \Modern_Validate_ArrayCount
     * @throws Zend_Validate_Exception
     */
    public function setMax($max)
    {
        if (null !== $this->getMin() && $max <= $this->getMin()) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("'max' must be greater than 'min'");
        }
        $this->_max = (int)$max;

        return $this;
    }

    /**
     * Returns true if and only if a key has been found in $context
     * and element is an array and contains the specified number of elements.
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function isValid($value, $context = array())
    {
        $value = $this->_findArray($context, $this->getKey());

        if (null === $value) {
            $this->_error(self::WRONG_KEY);
            return false;
        }

        if (!is_array($value)) {
            $this->_error(self::NOT_ARRAY);
            return false;
        }

        $this->_setValue($value);
        $this->_count = count($value);

        if (($this->_max !== null) && ($this->_count > $this->_max)) {
            $this->_error(self::TOO_MANY);
            return false;
        }

        if (($this->_min !== null) && ($this->_count < $this->_min)) {
            $this->_error(self::TOO_FEW);
            return false;
        }

        return true;
    }

    /**
     * @param array $context
     * @param string $key
     * @return mixed
     */
    protected function _findArray(array &$context, $key)
    {
        foreach ($context as $k => $value) {
            if ($k == $key) {
                return $value;
            }

            // recursive search in hash arrays
            if (is_array($value) && !is_int(key($value))) {
                $value = $this->_findArray($value, $key);
                if (null !== $value) {
                    return $value;
                }
            }
        }

        return null;
    }

}
