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
 * Photo album class.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Object_Permissions extends Modern_Facebook_Object
{
    /**
     * Tablica z uprawnieniami aplikacji
     *
     * @var array
     */
    protected $_permissions;

    /**
     * Id Uzytkownika
     *
     * @var string
     */
    protected $_fbUserId;

    /**
     * Pozwolenia użytkownika
     *
     * @var array
     */
    protected $_usersAccessPermissions;

    /**
     * Konstruktor
     */
    public function __construct($fbUserId)
    {
        $this->_fbUserId = $fbUserId;
        $this->_permissions = self::$_facebook->getOption('permissions');
    }

    /**
     * Zwraca tablice uprawnień użytkownika
     *
     * @return array
     */
    public function getUsersAccessPermissions()
    {
        if (null == $this->_usersAccessPermissions) {
            $this->_setUsersAccessPermissions();
        }
        return $this->_usersAccessPermissions;
    }

    /**
     * pyta Facebooka o uprawnienia użytkownika
     *
     * @return void
     */
    private function _setUsersAccessPermissions()
    {
        if (null != $this->_permissions) {
            $perm = implode(',', $this->_permissions);
            if (empty($perm)) {
                $this->_usersAccessPermissions = array();
            } else {
                $query = 'SELECT ' . $perm . ' FROM permissions WHERE uid=' . $this->_fbUserId;
                $result = $this->getApp()->fql($query);
                $this->_usersAccessPermissions = $result[0];
            }
        }
    }

    /**
     * Metoda zwraca czy uzytkownik ma okreslone uprawnienie
     *
     * @param string $permissionName
     * @return boolean
     */
    public function hasUserPermission($permissionName)
    {
        if (null === $this->_usersAccessPermissions) {
            $this->_setUsersAccessPermissions();
        }
        return (1 == $this->_usersAccessPermissions[$permissionName]) ? true : false;
    }

    /**
     * Usuwa uprawnienie o podanej nazwie
     *
     * @param string $permissionName
     * @return Zend_Http_Response
     */
    public function revokePermission($permissionName)
    {
        $client = new Zend_Http_Client(null, array(
                'maxredirects' => 0,
                'timeout' => 30
            ));
        $client->setMethod(Zend_Http_Client::GET);
        $client->setUri('https://api.facebook.com/method/auth.revokeExtendedPermission');
        $client->setParameterGet('access_token', $this->getApp()->getAccessToken());
        $client->setParameterGet('perm', $permissionName);
        return $client->request();
    }

}
