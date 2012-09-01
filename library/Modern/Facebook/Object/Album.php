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
class Modern_Facebook_Object_Album extends Modern_Facebook_Object
{
    /**
     * Id albumu
     *
     * @var string
     */
    protected $_id;

    /**
     * Url do danego albumu
     *
     * @var string
     */
    protected $_link;

    /**
     * Nazwa danego albumu
     *
     * @var string
     */
    protected $_name;

    /**
     * Liczba zdjec w albumie
     *
     * @var int
     */
    protected $_photoCount;

    /**
     * Objekt wlasciceila albumu
     *
     * @var object
     */
    protected $_owner;

    /**
     * Nazwa posiadacza albumu
     *
     * @var string
     */
    protected $_ownerName;

    /**
     * Rodzaj dostepnosci albumu
     *
     * @var string
     */
    protected $_privacy;

    /**
     * Opis albumu
     *
     * @var string
     */
    protected $_message;

    /**
     * Status obiektu
     *
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Konstruktor
     *
     * @param string $id
     */
    public function __construct($id = null)
    {
        if (null == $id) {
            $this->_readOnly = true;
        }

        $this->_id = $id;
    }

    /**
     * Zwraca status obiektu
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->_readOnly;
    }

    /**
     * Pobiera dane dla albumu
     *
     * @return array
     */
    public function getData()
    {
        return self::$_facebook->getApp('/' . $this->_id);
    }

    /**
     * Ustawia parametry albumu
     *
     * @param array $albumData
     * @return void
     */
    public function setData($albumData = null)
    {
        if (null == $albumData) {
            $albumData = $this->getData();
        }

        $this->_link = $albumData['link'];
        $this->_name = $albumData['name'];
        $this->_privacy = $albumData['privacy'];
        $this->_photoCount = $albumData['count'];
        $this->_owner = new Modern_Facebook_Object_User($albumData['from']['id']);
    }

    /**
     * Zwraca zdjęcia z danego albumu
     *
     * @return array
     */
    public function getPhotos()
    {
        $photos = self::$_facebook->getApp('/' . $this->_id . '/photos');
        return $photos['data'];
    }

    /**
     * Zwraca id albumu
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Zwraca adres Url do albumu
     *
     * @return string
     */
    public function getLink()
    {
        if (null == $this->_link) {
            $this->setData();
        }
        return $this->_link;
    }

    /**
     * Zwraca nazwe albumu
     *
     * @return string
     */
    public function getName()
    {
        if (null == $this->_name && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_name;
    }

    /**
     * Zwraca liczbe zdjec w albumie
     *
     * @return int
     */
    public function getPhotoCount()
    {
        if (null == $this->_photoCount) {
            $this->setData();
        }
        return $this->_photoCount;
    }

    /**
     * Zwraca rodzaj dostepnosci dla albumu
     *
     * @return string
     */
    public function getPrivacy()
    {
        if (null == $this->_privacy) {
            $this->setData();
        }
        return $this->_privacy;
    }

    /**
     * Zwraca Id wlasciciela albumu
     *
     * @return object
     */
    public function getOwner()
    {
        if (null == $this->_owner) {
            $this->setData();
        }
        return $this->_owner;
    }

    /**
     * Zwraca komentarze dotyczace zdjecia
     *
     * @return array
     */
    public function getComments()
    {
        return self::$_facebook->getApp('/' . $this->_id . '/comments');
    }

    /**
     * Zwraca opis dotyczy albumu
     *
     * @return array
     */
    public function getMessage()
    {
        if (null == $this->_message && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_message;
    }

    /**
     * Ustawia nazwe dla albumu
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->_isReadOnly();
        $this->_name = $name;
    }

    /**
     * Ustawia opis albumu
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->_isReadOnly();
        $this->_message = $message;
    }

    /**
     * Dodanie nowego albumu uzytkownikowi o id $ownerId
     *
     * @param string $ownerId
     * @return void | Modern_Facebook_Exception
     */
    public function add($ownerId)
    {
        $this->_isReadOnly();

        if (null == $this->getName() || null == $this->getMessage()) {
            throw new Modern_Facebook_Exception(
                'Nie ustawiono nazwy lub opisu albumu'
            );
        }

        $fb = Modern_Application::getInstance()->getResource('facebook');
        $client = $fb->getClient();
        $client->setParameterPost('name', $this->getName());
        $client->setParameterPost('message', $this->getMessage());
        $url = "https://graph.facebook.com/$ownerId/albums";
        $client->setUri($url);
        $response = $client->request();

        if ($response->isError()) {
            $error = Zend_Json::decode($response->getBody());
            throw new Modern_Facebook_Exception(
                'Kod błędu:' . $response->getStatus() . ' ' . $response->getMessage() . ' : ' . $error['error']['message']
            );
        }
    }

    /**
     * Rzuca wyjatek w przypadku kiedy obiekt ma status readOnly
     *
     * @return void | Modern_Facebook_Exception
     */
    private function _isReadOnly()
    {
        if ($this->_readOnly) {
            throw new Modern_Facebook_Exception(
                'Obiekt tylko do odczytu!'
            );
        }
    }

}
