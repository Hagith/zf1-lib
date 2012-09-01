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
 * Facebook user.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Object_User extends Modern_Facebook_Object
{
    /**
     * Id użytkownika
     *
     * @var string
     */
    protected $_id;

    /**
     * Link do profilu
     *
     * @var string
     */
    protected $_link;

    /**
     * Nazwa uzytkownika
     *
     * @var string
     */
    protected $_name;

    /**
     * Płeć użytkownika
     *
     * @var string
     */
    protected $_gender;

    /**
     * Modern_Facebook_Object_Permissions
     *
     * @var object
     */
    protected $_permissions;

    /**
     * Dane profilowe.
     *
     * @var array
     */
    protected $_profile;

    /**
     * Konstruktor
     *
     * @param string $fbUserID
     */
    public function __construct($fbUserID)
    {
        $this->_id = $fbUserID;
    }

    /**
     * Zwraca Url obrazka, będącego avatarem użytkownika
     *
     * @param boolean $large
     * @return string
     */
    public function getAvatarUrl($large = false)
    {
        $imgUrl = 'https://graph.facebook.com/' . $this->_id . '/picture';
        if ($large) {
            $imgUrl.='?type=large';
        }
        return $imgUrl;
    }

    /**
     * Zwraca znajomych użytkownika
     *
     * @return array
     */
    public function getFriends()
    {
        $friends = self::$_app->getApp('/' . $this->_id . '/friends');
        return $friends['data'];
    }

    /**
     * Zwaraca zdjęcia, na których użytkownik jest otagowany
     * Wymaga uprawnienia user_photos
     *
     * @return array
     */
    public function getTagsPhotos()
    {
        $tagsPhotos = self::$_app->getApp('/' . $this->_id . '/photos');
        return $tagsPhotos['data'];
    }

    /**
     * Zwraca filmy użytkownika
     * Wymaga uprawnienia user_videos
     *
     * @return array
     */
    public function getVideos()
    {
        $videos = self::$_app->getApp('/' . $this->_id . '/videos');
        return $videos['data'];
    }

    /**
     * Zwraca albumy użytkownika
     * Wymaga uprawnienia user_photos
     *
     * @return array
     */
    public function getAlbums()
    {
        $albums = self::$_app->getApp('/' . $this->_id . '/albums');
        return $albums['data'];
    }

    /**
     * Pokazuje wiadomości na tablicy użytkownika
     * Wymaga uprawnienia read_stream
     *
     * @return array
     */
    public function getPosts()
    {
        $posts = self::$_app->getApp('/' . $this->_id . '/posts');
        return $posts['data'];
    }

    /**
     * Zwraca informacje dotyczace profilu użytkownika
     * Dane te moga roznic sie w przypadku zaakceptowania dodatkowych praw dostepu
     *
     * @return array
     */
    public function getProfile($reload = false)
    {
        if (null === $this->_profile || $reload) {
            $this->_profile = self::$_app->getApp('/' . $this->_id);
        }
        return $this->_profile;
    }

    /**
     * Ustawia dane użytkownika
     *
     * @return void
     */
    public function setProfile($data = null)
    {
        if (null == $data) {
            $data = $this->getProfile();
        }
        $this->_name = $data['name'];
        $this->_link = $data['link'];
        $this->_gender = $data['gender'];
    }

    /**
     * Zwraca id użytkownika
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Zwraca nazwę użytkownika
     *
     * @return string
     */
    public function getName()
    {
        if (null == $this->_name) {
            $this->setProfile();
        }
        return $this->_name;
    }

    /**
     * Zwraca płeć użytkownika
     *
     * @return string
     */
    public function getGender()
    {
        if (null == $this->_gender) {
            $this->setProfile();
        }
        return $this->_gender;
    }

    /**
     * Zwraca link do profilu użytkownika
     *
     * @return string
     */
    public function getLink()
    {
        if (null == $this->_link) {
            $this->setProfile();
        }
        return $this->_link;
    }

    /**
     * Zrzutowanie do tablicy
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'gender' => $this->getGender(),
            'link' => $this->getLink()
        );
    }

    /**
     * Ustawia objekt Facebook_Model_Permission
     *
     * @return void
     */
    private function _setPermissions()
    {
        $this->_permissions = new Modern_Facebook_Object_Permissions($this->getId());
    }

    /**
     * Zwraca wszystkie uprawnienia użytkownika w postaci tablicy
     *
     * @return Modern_Facebook_Object_Permissions
     */
    public function getPermissions()
    {
        if (null === $this->_permissions) {
            $this->_setPermissions();
        }

        return $this->_permissions;
    }

}
