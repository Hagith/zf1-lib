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
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * @category    Modern
 * @package     Modern_Facebook
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook
{
    /**
     * @var  Modern/Facebook/Graph/facebook.php
     */
    protected $_api;

    /**
     * Api key aplikacji
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * Api secret aplikacji
     *
     * @var string
     */
    protected $_apiSecret;

    /**
     * Id aplikacji
     *
     * @var int
     */
    protected $_appId;

    /**
     * Prawa jakie ma wymuszać aplikacja
     * Sczegóły
     * http://developers.facebook.com/docs/authentication/permissions
     *
     * @var array
     */
    protected $_permission;

    /**
     * Url strony aplikacji na facebooku
     *
     * @var string
     */
    protected $_canvasUrl;

    /**
     * Access token
     *
     * @var string
     */
    protected $_accessToken;

    /**
     * Dane sesyjne
     *
     * @var array
     */
    protected $_sessionData;

    /**
     * @var Zend_Http_Client
     */
    protected $_client;

    /**
     * Access token APLIKACJI
     *
     * @var string
     */
    protected $_appAccessToken;

    /**
     * Obiekt zalogowanego Usera
     *
     * @var object  Facebook_Model_User
     */
    protected $_loggedUser;

    /**
     * Zawiera dane konfiguracyjne z facebook.ini
     *
     * @var array
     */
    protected $_options;

    /**
     * Powoluje obiekt facady
     */
    public function __construct($options)
    {
        $this->_setConfig($options);
        $this->_setClient();
        $this->_options = $options;

        Modern_Facebook_Object::setApp($this);

        //czy aplikacja jest w canvasie
        if (1 == (int) $options['canvasApp']) {
            $this->checkCanvasPermission();
        }
    }

    /**
     * Sprawdza uprawnienia dla aplikacji w Canvasie
     *
     * @return void
     */
    public function checkCanvasPermission()
    {
        if (!$this->hasCanvasPermission()) {
            $this->_accessRedirect();
        } else {
            $this->_setVariablesFromSession();
        }
    }

    /**
     * Ustawia konfiguracje
     *
     * @param array $options
     * @return void
     */
    private function _setConfig($options)
    {
        $config = array(
            'appId' => $options['appId'],
            'secret' => $options['secret'],
            'cookie' => (boolean) $options['cookie'],
        );

        $this->_api = new Facebook($config);
        $this->_appId = $options['appId'];
        $this->_canvasUrl = $options['canvasUrl'];
        $this->_apiSecret = $options['apiSecret'];
        $this->_apiKey = $options['apiKey'];
        $this->_permission = $options['permission'];
    }

    /**
     * metoda przekierowywuje
     *
     * @return void
     */
    private function _accessRedirect()
    {
        //przekierowanie na strone wymuszajaca uprawnienia
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . PHP_EOL;
        echo '<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" >' . PHP_EOL;
        echo '<head>' . PHP_EOL;
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . PHP_EOL;
        echo '<title>Brak uprawnien</title>' . PHP_EOL;
        echo '</head>' . PHP_EOL;
        echo '<body>' . PHP_EOL;
        echo '<script type="text/javascript"> top.location.href = \'' . $this->genereteAccessUrl() . '\'; </script>' . PHP_EOL;
        echo '</body>' . PHP_EOL;
        echo '</html>' . PHP_EOL;
        exit;
    }

    /**
     * Przypisuje parametry z sesji
     *
     * @return void
     */
    private function _setVariablesFromSession()
    {
        $this->_sessionData = $this->getSessionData();
        $this->setAccessToken($this->_sessionData['access_token']);
    }

    /**
     * Ustawia Zend_Http_Client
     *
     * @return void
     */
    private function _setClient()
    {
        $this->_client = new Zend_Http_Client(null, array(
                'maxredirects' => 0,
                'timeout' => 30
            ));
        $this->_client->setMethod(Zend_Http_Client::POST);
    }

    /**
     * Zwraca w postaci tablicy dane zawarte w sessji
     * Tablica ta zawiera nastepujace dane:
     * 'uid'
     * 'session_key'
     * 'secret'
     * 'expires'
     * 'access_token'
     *  'sig'
     *
     * @return array
     */
    public function getSessionData()
    {
        return $this->_api->getSession();
    }

    /**
     * Sprawdza czy dany użytkownik jest zalogowany
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->_api->getUser() ? true : false;
    }

    /**
     * Metoda sprawdza czy użytkownik posiada prawa do aplikacji
     *
     * @return boolean
     */
    public function hasCanvasPermission()
    {
        if ($this->isConnected()) {
            $userPerms = $this->getLoggedUser()->getPermissions()->getUsersAccessPermissions();
            foreach ($this->_permission as $perm) {
                if (!isset($userPerms[$perm]) || !$userPerms[$perm]) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Metoda generuje link na ktory powinien zostac przekierowany uzytkownik, ktory nie posiada praw
     * dostepu do aplikacji
     *
     * Dokladna struktura configa znajduje sie tu
     * http://wiki.developers.facebook.com/index.php/Authorization_and_Authentication_for_Desktop_Applications
     *
     * @param array $config
     * @return string
     */
    public function genereteAccessUrl($config = null)
    {
        if (null == $config) {
            $config = array(
                'canvas' => 1,
                'fbconnect' => 0,
            );
            //gdy aplikacja posiada dodatkowe prawa
            if (!empty($this->_permission)) {
                $config['req_perms'] = implode(',', $this->_permission);
            }
        }

        $loginUrl = $this->_api->getLoginUrl($config);
        return $loginUrl;
    }

    /**
     * Zwraca Api Key aplikacji
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Zwraca Api Secret Aplikacji
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->_apiSecret;
    }

    /**
     * Zwraca Id aplikacji Facebookowej
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->_appId;
    }

    /**
     * Zwraca canvasUrl - adres URL aplikacji na Facebooku
     *
     * @return string
     */
    public function getCanvasUrl()
    {
        return $this->_canvasUrl;
    }

    /**
     * Zwraca tablice uprawnien aplikacji
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->_permission;
    }

    /**
     * Zwraca access token użytkownika
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * Ustawia accessToken
     *
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
    }

    /**
     * Zwraca objekt obecnie zalogowanego
     *
     * @return Modern_Facebook_Object_User
     */
    public function getLoggedUser()
    {
        if (null === $this->_loggedUser) {
            $this->_sessionData = $this->getSessionData();
            if (empty($this->_sessionData)) {
                $this->_accessRedirect();
            }
            $this->_loggedUser = new Modern_Facebook_Object_User($this->_sessionData['uid']);
        }

        return $this->_loggedUser;
    }

    /**
     * Metoda wykonuje dowolne zapytanie FQL-a
     * Opis struktury tabel Facebooka znajduje sie tutaj:
     * http://developers.facebook.com/docs/reference/fql/
     *
     * @param string $query
     * @return array
     */
    public function fql($query)
    {
        $param = array(
            'method' => 'fql.query',
            'query' => $query,
        );
        return $this->_api->api($param);
    }

    /**
     * Zwraca object Facebook [ 'Facebook/Model/Api/facebook.php' ]
     *
     * @return array
     */
    public function getApp($graphQuery, $urlParams = null)
    {
        $graphQuery.= (null == $urlParams) ? '?access_token=' . $this->getAccessToken() : '?' . urldecode(http_build_query($urlParams));
        return $this->_api->api($graphQuery);
    }

    /**
     * Metoda zwraca obiekt Modern/Facebook/Graph/facebook.php
     *
     * @return Facebook
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Zwraca access token aplikacji
     *
     * @return string
     */
    public function getAppAccessToken()
    {
        if (null === $this->_appAccessToken) {
            $this->_setAppAccessToken();
        }
        return $this->_appAccessToken;
    }

    /**
     * Ustawia access token dla aplikacji
     *
     * @return void
     */
    private function _setAppAccessToken()
    {
        $params = array(
            'client_id' => $this->getAppId(),
            'client_secret' => $this->getApiSecret(),
            'type' => 'client_cred'
        );
        $tmpAccessToken = $this->getApp('/oauth/access_token', $params);
        $this->_appAccessToken = substr($tmpAccessToken, 13);
    }

    /**
     * Zwraca Zend_Http_Client
     *
     * @return object
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Pobiera podstawowe dane o uizytkownikach ktorych idiki sa zawarte w $ids
     *
     * @param array $ids
     * @return array
     */
    public function getMultiUserInfo($ids)
    {
        if (count($ids) > 0) {
            $ids = implode(',', $ids);
        } else {
            return array();
        }

        $fqlQuery = "SELECT uid, name, sex FROM user WHERE uid IN ($ids)";
        return $this->fql($fqlQuery);
    }

    /**
     * Metoda zamienia sessionKey na Accesstoken
     *
     * @param string $sessionKey
     */
    public function convertSessionKeyToAccesstoken($sessionKey)
    {
        $this->_client->setUri('https://graph.facebook.com/oauth/exchange_sessions');
        $this->_client->setParameterPost('type', 'client_cred');
        $this->_client->setParameterPost('client_id', $this->getAppId());
        $this->_client->setParameterPost('client_secret', $this->getApiSecret());
        $this->_client->setParameterPost('sessions', $sessionKey);

        return $response = $this->_client->request();
    }

    /**
     * Zwraca wartośc podanej zmiennej konfiguracyjnej
     *
     * @param string $optionName
     * @return string | false (w przypadku kiedy nie ma takie opcji)
     */
    public function getOption($optionName)
    {
        if (!array_key_exists($optionName, $this->_options)) {
            return false;
        }
        return $this->_options[$optionName];
    }

}
