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
class Modern_Facebook_Object_Subscription extends Modern_Facebook_Object
{
    /**
     * Typ obiektu - najczęściej user albo permission
     *
     * @var string
     */
    protected $_object;

    /**
     * Lista pól danego obiektu
     *
     * @var array
     */
    protected $_fields;

    /**
     * Adres zwrotny Url
     *
     * @var string
     */
    protected $_callback_url;

    /**
     * Zwraca typ objektu
     *
     * @return string
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Zwraca których pól dotyczy obiekt
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Adres strony, na która będzie przychodzić subskrycja z FB
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->_callback_url;
    }

    /**
     * Ustawia objekt
     *
     * @param string $object - możliwe wartości to 'user','permission','errors'
     * @return void
     */
    public function setObject($object)
    {
        if (!in_array($object, array('user', 'permissions', 'errors'))) {
            throw new Modern_Facebook_Exception(
                'Kod błędu:' . $response->getStatus() . ' ' . $response->getMessage() . ' : ' . $error['error']['message']
            );
        }
        $this->_object = $object;
    }

    /**
     * Ustawia typ pól dla obiektu
     *
     * @param array $fields
     * @return void
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    /**
     * ustawia stronę na którą ma przychodzić subskrypcja
     *
     * @param string $url
     * @return void
     */
    public function setCallbackUrl($url)
    {
        $this->_callback_url = $url;
    }

    /**
     * Sprawdza czy subskrypcja dla danej aplikacji jest podłączona
     *
     * @return array
     */
    public function check()
    {
        $appId = $this->getApp()->getAppId();
        $appAccessToken = $this->getApp()->getAppAccessToken();
        $this->getApp()->setAccessToken($appAccessToken);
        return self::$_facebook->getApp('/' . $appId . '/subscriptions');
    }

    /**
     * Dodaje subskrypcję
     *
     * @return Zend_Http_Response
     */
    public function add()
    {
        if (null == $this->getObject() ||
            null == $this->getFields() ||
            null == $this->getCallbackUrl()
        ) {
            throw new Modern_Facebook_Exception(
                'Nie ustawiono wystarczającej ilości parametrów dla subskrycji'
            );
        }

        $fb = $this->getApp();
        $client = $fb->getClient();
        $client->setParameterPost('access_token', $fb->getAppAccessToken());
        $client->setParameterPost('object', $this->getObject());
        $client->setParameterPost('fields', $this->getFields());
        $client->setParameterPost('callback_url', $this->getCallbackUrl());
        $client->setParameterPost('verify_token', $fb->getOption('subscriptionToken'));

        $appId = $fb->getAppId();
        $url = "https://graph.facebook.com/$appId/subscriptions";
        $client->setUri($url);

        return $client->request();
    }

    /**
     * Usuwa wszystkie subskrypcje
     *
     * @return Zend_Http_Response
     */
    public function delete()
    {
        $fb = $this->getApp();
        $client = $fb->getClient();
        $client->setMethod(Zend_Http_Client::DELETE);
        $client->setParameterPost('access_token', $fb->getAppAccessToken());

        $appId = $fb->getAppId();
        $url = "https://graph.facebook.com/$appId/subscriptions";
        $client->setUri($url);

        return $client->request();
    }

    /**
     * Zwraca w postaci tablicy obiekt subscription
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'object' => $this->getObject(),
            'callbackUrl' => $this->getCallbackUrl(),
            'fields' => $this->getFields(),
            'active' => $this->getActive(),
        );
    }

}
