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
 *
 * @todo Na dzien 21.06.2010 graph Api nie umozliwia ustawiania dodatkowego actions_linka. W
 *       przyszlosci ma sie to zmienic wiecej na ten temat:
 *       http://forum.developers.facebook.com/viewtopic.php?pid=223444
 */
class Modern_Facebook_Object_Post extends Modern_Facebook_Object
{
    /**
     * id postu
     *
     * @var string
     */
    protected $_id;

    /**
     * Objekt uzytkownika - wlasciciela postu
     *
     * @var object
     */
    protected $_owner;

    /**
     *
     * @var <type>
     */
    protected $_to;

    /**
     * Wiadomosc
     *
     * @var string
     */
    protected $_message;

    /**
     * Url zdjecia zawartego w danym poscie.
     * UWAGA!!!! Url do zdjecia musi zawierac adres aplikacji
     *
     * @var string
     */
    protected $_picture;

    /**
     * Link zawarty pod naglowkiem posta
     * UWAGA!!! Link musi przekierowywac na jakis adres naszej aplikacji
     *
     * @var <type>
     */
    protected $_link;

    /**
     * Nazwa postu
     *
     * @var string
     */
    protected $_name;

    /**
     * Naglowek postu
     *
     * @var string
     */
    protected $_caption;

    /**
     * Opis
     *
     * @var string
     */
    protected $_description;

    /**
     * zrodlo
     *
     * @var string
     */
    protected $_source;

    /**
     * Url ikonki
     *
     * @var string
     */
    protected $_icon;

    /**
     * Malutki link w opisie postu
     *
     * @var string
     */
    protected $_attribution;

    /**
     * Tablica zawierająca nazwe linków wraz z adresami. Linki te są umieszczone na dole postu
     * Uwaga!!! Tablica ta ma okreśoną strukturę:
     * array
      0 =>
      array
      'name' => string 'Comment'
      'link' => string 'http://www.facebook.com/1696036535/posts/130284977001050'
      1 =>
      array
      'name' => string 'Like'
      'link' => string 'http://www.facebook.com/1696036535/posts/130284977001050'
      2 =>
      array
      'name' => string 'Podejmij wyzwanie'
      'link' => string 'http://apps.facebook.com/krol-strzelcow/bet/game/bet-response/id/1060/'

     * *  @var array
     */
    protected $_actions;

    /**
     * Czas utworzenia postu
     *
     * @var string
     */
    protected $_created_time;

    /**
     * Zmienna informująca czy obiekt jest tylko do odczyty
     *
     * @var boolean
     */
    public $_readOnly = false;

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
     * @param array | null $data
     * @return void
     */
    public function setData($data = null)
    {
        if (null == $data) {
            $data = $this->getData();
        }

        $this->_owner = new Facebook_Model_User($data['from']['id']);

        foreach ($data as $key => $value) {
            $this->{'_' . $key} = $value;
        }
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
        if (null == $this->_link && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_link;
    }

    /**
     * Zwraca nazwe
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
     * Zwraca dowolny atrybut obiektu o ile takowy istnieje
     *
     * @param string $data
     * @return string | object | int
     */
    public function __get($name)
    {
        $n = '_' . $name;
        return isset($this->$n) ? $this->$n : null;
    }

    /**
     * Zwraca adres Url do albumu
     *
     * @return string
     */
    public function getMessage()
    {
        if (null == $this->_message && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_message;
    }

    /**
     * Zwraca nazwe postu
     *
     * @return string
     */
    public function getCaption()
    {
        if (null == $this->_caption && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_caption;
    }

    /**
     * Zwraca obiekt wlasciciela posta
     *
     * @return object
     */
    public function getOwner()
    {
        if (null == $this->_owner && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_owner;
    }

    /**
     * Zwraca komentarze dotyczace postu
     *
     * @return array
     */
    public function getComments()
    {
        return self::$_facebook->getApp('/' . $this->_id . '/comments');
    }

    /**
     * Zwraca komentarze dotyczace postu
     *
     * @return array
     */
    public function getTo()
    {
        if (null == $this->_to && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_to;
    }

    /**
     * Zwraca URL zdjecia w poscie
     *
     * @return strig
     */
    public function getPicture()
    {
        if (null == $this->_picture && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_picture;
    }

    /**
     * Zwraca opis zawarty w poscie
     *
     * @return strig
     */
    public function getDescription()
    {
        if (null == $this->_description && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_description;
    }

    /**
     * Zwraca parametr zrodla
     *
     * @return strig
     */
    public function getSource()
    {
        if (null == $this->_source && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_source;
    }

    /**
     * Zwraca URL z ikona Postu
     *
     * @return string
     */
    public function getIcon()
    {
        if (null == $this->_icon && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_icon;
    }

    /**
     * Zwraca atrybuty Postu
     *
     * @return strig
     */
    public function getAttributian()
    {
        if (null == $this->_attributian && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_attributian;
    }

    /**
     * Zwraca atrybuty Postu
     *
     * @return array
     */
    public function getActions()
    {
        if (null == $this->_actions && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_actions;
    }

    /**
     * Zwraca date powstania Postu
     *
     * @return array
     */
    public function getCreatedTime()
    {
        if (null == $this->_created_time && false == $this->_readOnly) {
            $this->setData();
        }
        return $this->_created_time;
    }

    /**
     * Ustawia message
     *
     * @param string $message
     * @return void
     */
    public function setMessage($message)
    {
        $this->_isReadOnly();
        $this->_message = $message;
    }

    /**
     * Ustawia link
     *
     * @param string $link
     * @return void
     */
    public function setLink($link)
    {
        $this->_isReadOnly();
        $this->_link = $link;
    }

    /**
     * Ustawia name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->_isReadOnly();
        $this->_name = $name;
    }

    /**
     * Ustawia description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->_isReadOnly();
        $this->_description = $description;
    }

    /**
     * Ustawia naglowek
     *
     * @param string $caption
     * @return void
     */
    public function setCaption($caption)
    {
        $this->_isReadOnly();
        $this->_caption = $caption;
    }

    /**
     * Ustawia zdjecie [majlepiej kwadrat 70x70 px]
     *
     * @param string $picture
     * @return void
     */
    public function setPicture($picture)
    {
        $this->_isReadOnly();
        $this->_picture = $picture;
    }

    /**
     * Dodaje posta na tablicy
     *
     * @param string/object $ownerId
     * @return Zend_Http_Response
     */
    public function add($owner)
    {
        $ownerId = $owner;
        if ('object' == gettype($owner)) {
            $ownerId = $owner->getId();
        }

        $this->_isReadOnly();

        $fb = Modern_Application::getInstance()->getResource('facebook');
        $client = $fb->getClient();

        //ustawienie parametrow
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);

        foreach ($props as $prop) {
            if ('_app' != $prop->getName()) {
                $key = substr($prop->getName(), 1, strlen($prop->getName()));
                $client->setParameterPost($key, $this->{$prop->getName()});
            }
        }
        $url = "https://graph.facebook.com/$ownerId/feed";
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
     * Rzuca wyjątek w przypadku kiedy obiekt ma status readOnly
     *
     * @return void
     */
    private function _isReadOnly()
    {
        if ($this->_readOnly) {
            throw new Modern_Facebook_Exception(
                'Obiekt tylko do odczytu!'
            );
        }
    }

    /**
     * Zwraca object POST zrzutowany na tablicę
     *
     * @return array
     */
    public function toArray()
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);

        $data = array();
        foreach ($props as $prop) {
            if ('_app' != $prop->getName()) {
                $key = substr($prop->getName(), 1, strlen($prop->getName()));
                $data[$key] = $this->{$prop->getName()};
            }
        }
        return $data;
    }

}
