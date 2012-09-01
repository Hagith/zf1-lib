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
 * @package     Modern_Facebook
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Modern_Facebook_Validate_Abstract */
require_once 'Modern/Facebook/Validate/Abstract.php';

/**
 * Check if logged user is friend of given user.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Validate_IsFriend extends Modern_Facebook_Validate_Abstract
{
    const NOT_FRIEND = 'notFriend';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_FRIEND => "Wybrana osoba nie jest na liście Twoich znajomych",
    );

    /**
     * Defined by Zend_Validate_Interface.
     *
     * Return true if user liked profile page, false otherwise.
     *
     * @param string $value Logged Facebook user ID
     * @return boolean
     */
    public function isValid($value)
    {
        $friends = $this->getFacebook()->getLoggedUser()->getFriends();

        foreach ($friends as &$friend) {
            if ($friend['id'] == $value)
                return true;
        }

        $this->_error(self::NOT_FRIEND);
        return false;
    }

}
