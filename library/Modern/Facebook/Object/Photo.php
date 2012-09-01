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
 * Photo class.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Object
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Object_Photo extends Modern_Facebook_Object
{
    /**
     * Wysokosc zdjecia
     *
     * @var int
     */
    protected $_height;

    /**
     * Szeroksoc zdjecia
     *
     * @var int
     */
    protected $_width;

    /**
     * Id zdjecia
     *
     * @var string
     */
    protected $_id;

    /**
     * Url do do zdjecia na FB
     *
     * @var  string
     */
    protected $_link;

    /**
     * Nazwa zdjecia
     *
     * @var string
     */
    protected $_name;

    /**
     * Url - sciezka do zdjecia na serwerze FB
     *
     * @var string
     */
    protected $_source;

    /**
     * Zwraca URl do miniaturki zdjecia
     *
     * @var string
     */
    protected $_sourceThumb;

    /**
     * Konstruktor
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->_id = $id;
    }

    /**
     * Pobiera dane o zdjeciu
     */
    public function getData()
    {
        return self::$_facebook->getApp('/' . $this->_id);
    }

    /**
     * Ustawia dane zdjecia
     *
     * @param array $data
     */
    public function setData($data = null)
    {
        if (null == $data) {
            $data = $this->getData();
            return;
        }

        $this->_height = $data['height'];
        $this->_width = $data['width'];
        $this->_link = $data['link'];
        $this->_name = $data['name'];
        $this->_sourceThumb = $data['picture'];
        $this->_source = $data['source'];
    }

    /**
     * Zwraca wysokość zdjęcia
     *
     * @return int
     */
    public function getHeight()
    {
        if (null == $this->_height) {
            $this->setData();
        }
        return $this->_height;
    }

    /**
     * Zwraca szerokosc zdjecia
     *
     * @return int
     */
    public function getWidth()
    {
        if (null == $this->_width) {
            $this->setData();
        }
        return $this->_width;
    }

    /**
     * Zwraca URL zdjecia na serwerze FB
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
     * Zwraca Id uzytkownika
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Zwraca nazwe zdjecia
     *
     * @return string
     */
    public function getName()
    {
        if (null == $this->_name) {
            $this->setData();
        }
        return $this->_name;
    }

    /**
     * Zwraca adres URL
     *
     * @return string
     */
    public function getSource()
    {
        if (null == $this->_source) {
            $this->setData();
        }
        return $this->_source;
    }

    /**
     * Zwraca adres URL miniaturki
     *
     * @return string
     */
    public function getSourceThumb()
    {
        if (null == $this->_sourceThumb) {
            $this->setData();
        }
        return $this->_sourceThumb;
    }

    /**
     * Rzutuje obiekt na tablice
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'height' => $this->getHeight(),
            'width' => $this->getWidth(),
            'link' => $this->getLink(),
            'name' => $this->getName(),
            'sourceThumb' => $this->getSourceThumb(),
            'source' => $this->getSource(),
        );
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

}
