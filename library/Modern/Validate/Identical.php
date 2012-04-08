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
 * Extended Identical validator. Added recursive context search.
 *
 * @category    Modern
 * @package     Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Validate_Identical extends Zend_Validate_Identical
{
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @param  array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->_setValue((string) $value);

        if (is_array($context)) {
            $token = $this->_searchInContext($context, $this->getToken());
        } else {
            $token = $this->getToken();
        }

        if ($token === null) {
            $this->_error(self::MISSING_TOKEN);
            return false;
        }

        $strict = $this->getStrict();
        if (($strict && ($value !== $token)) || (!$strict && ($value != $token))) {
            $this->_error(self::NOT_SAME);
            return false;
        }

        return true;
    }

    /**
     * @param array $context
     * @param string $key
     * @return mixed
     */
    protected function _searchInContext(array &$context, $key)
    {
        foreach ($context as $k => $value) {
            if ($k == $key) {
                return $value;
            }

            // recursive search in hash arrays
            if (is_array($value) && !is_int(key($value))) {
                $value = $this->_searchInContext($value, $key);
                if (null !== $value) {
                    return $value;
                }
            }
        }

        return null;
    }

}
