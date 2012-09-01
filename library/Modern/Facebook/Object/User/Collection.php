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
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * List of users.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Object_User_Collection
{
    /**
     * Lista użytkowników
     *
     * @var array
     */
    protected $_users = array();

    /**
     * Konstruktor
     *
     * @param array $users
     */
    public function __construct($users)
    {
        if (!is_array($users)) {
            $users = array($users);
        }

        $fb = Modern_Application::getInstance()->getResource('facebook');
        $usersData = $fb->getMultiUserInfo($users);

        foreach ($usersData as $data) {
            $user = new Modern_Facebook_Object_User($data['uid']);
            $user->setProfile($data);
            $this->_users[$data['uid']] = $user;
        }
    }

    /**
     * Zrzutowanie na tablice
     *
     * @return array
     */
    public function toArray()
    {
        $users = array();
        foreach ($this->_users as $uid => $user) {
            $users[$uid] = $user->toArray();
        }
        return $users;
    }

}
