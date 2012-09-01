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
     * @var array
     */
    protected $_options = array(
        'appId' => null,
        'secret' => null,
        'cookie' => true, // (JavaScript SDK) enable cookies to allow the server to access the session
        'status' => true, // (JavaScript SDK) check login status
        'xfbml' => true,  // (JavaScript SDK) parse XFBML
        'fileUpload' => false, // (PHP SDK) https://developers.facebook.com/docs/reference/php/facebook-setFileUploadSupport/
        'canvas' => null, // facebook canvas application URL
        'forceRedirectTo' => false, // force user redirect to facebook canvas
        'tab' => null, // facebook page tab URL
        'fanpageId' => null,
        'fanpageUrl' => null,
        'permissions' => array(), // http://developers.facebook.com/docs/authentication/permissions
    );

    /**
     * @var Facebook
     */
    protected $_sdk;

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
     * @param array|Zend_Config $options
     */
    public function __construct($options)
    {
        $this->setOptions($options);

        Modern_Facebook_Object::setFacebook($this);
    }

    /**
     * @param array|Zend_Config $options
     * @return Modern_Facebook
     * @thrown Modern_Facebook_Exception
     */
    public function setOptions($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return \Modern_Facebook
     * @throws Modern_Facebook_Exception
     */
    public function setOption($name, $value)
    {
        if (in_array($name, array('cookie', 'status', 'xfbml'))) {
            $value = (bool) $value;
        }
        $setter = 'set' + ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
            return $this;
        }

        if (array_key_exists($name, $this->_options)) {
            $this->_options[$name] = $value;
            return $this;
        }

        throw new Modern_Facebook_Exception("Unknown option '$key'");
    }

    /**
     * Get option value.
     *
     * @param string $name
     * @return mixed
     * @thrown Modern_Facebook_Exception
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->_options)) {
            throw new Modern_Facebook_Exception("Unknown option '$name'");
        }

        return $this->_options[$name];
    }

    /**
     * @param array|string $perms
     * @return \Modern_Facebook
     * @throws Modern_Facebook_Exception
     */
    public function setPermissions($perms)
    {
        if (is_string($perms)) {
            $perms = preg_split('/,/', $perms, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (!is_array($perms)) {
            throw new Modern_Facebook_Exception('Permissions must be string or array');
        }

        $this->_options['permissions'] = $perms;

        return $this;
    }

    /**
     * @param string|boolean $value
     * @return \Modern_Facebook
     * @throws Modern_Facebook_Exception
     */
    public function setForceRedirectTo($value)
    {
        if (!$value) {
            $this->_options['forceRedirectTo'] = false;
            return $this;
        }

        if (in_array($value, arrat('canvas', 'tab'))) {
            $this->_options['forceRedirectTo'] = $value;
            return $this;
        }

        throw new Modern_Facebook_Exception("Unknown redirect target '$value'");
    }

    /**
     * @return boolean
     */
    public function isForceRedirectTo()
    {
        return (bool) $this->_options['forceRedirectTo'];
    }

    /**
     * @return string
     */
    public function getForceRedirectTargetUrl()
    {
        if ($this->_options['forceRedirectTo']) {
            return $this->_options[$this->_options['forceRedirectTo']];
        }

        return null;
    }

    /**
     * @param array $params
     * @return string
     */
    public function getLoginUrl(array $params = array())
    {
        if (!isset($params['scope']) && !empty($this->_options['permissions'])) {
            $params['scope'] = $this->_options['permissions'];
        }

        return $this->getSdk()->getLoginUrl($params);
    }

    /**
     * @return Zend_Http_Client
     */
    public function getClient()
    {
        if (null == $this->_client) {
            $client = new Zend_Http_Client(null, array(
                'maxredirects' => 0,
                'timeout' => 30
            ));
            $this->setClient($client);
        }

        return $this->_client;
    }

    /**
     * @return Modern_Facebook
     */
    public function setClient(Zend_Http_Client $client)
    {
        $this->_client = $client;
        $this->_client->setMethod(Zend_Http_Client::POST);

        return $this;
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
        return $this->getSdk()->getSession();
    }

    /**
     * Sprawdza czy dany użytkownik jest zalogowany
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->getSdk()->getUser() ? true : false;
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
            foreach ($this->_options['permissions'] as $perm) {
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
            if (!empty($this->_options['permissions'])) {
                $config['req_perms'] = implode(',', $this->_options['permissions']);
            }
        }

        return $this->getSdk()->getLoginUrl($config);
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

        return $this->getSdk()->api($param);
    }

    /**
     * Zwraca object Facebook [ 'Facebook/Model/Api/facebook.php' ]
     *
     * @return array
     * @todo rename to graphApi
     */
    public function getApp($graphQuery, $urlParams = null)
    {
        $graphQuery.= (null == $urlParams) ? '?access_token=' . $this->getAccessToken() : '?' . urldecode(http_build_query($urlParams));
        return $this->getSdk()->api($graphQuery);
    }

    /**
     * @param Facebook $sdk
     * @return \Modern_Facebook
     */
    public function setSdk(Facebook $sdk)
    {
        $this->_sdk = $sdk;

        return $this;
    }

    /**
     * @return Facebook
     */
    public function getSdk()
    {
        if (null == $this->_sdk) {
            $this->_sdk = new Facebook(array(
                'appId' => $this->_options['appId'],
                'secret' => $this->_options['secret'],
                'fileUpload' => $this->_options['fileUpload'],
            ));
        }

        return $this->_sdk;
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
            'client_id' => $this->getOption('appId'),
            'client_secret' => $this->getOption('secret'),
            'type' => 'client_cred'
        );
        $tmpAccessToken = $this->getApp('/oauth/access_token', $params);
        $this->_appAccessToken = substr($tmpAccessToken, 13);
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
        $this->_client->setParameterPost('client_id', $this->getOption('appId'));
        $this->_client->setParameterPost('client_secret', $this->getOption('secret'));
        $this->_client->setParameterPost('sessions', $sessionKey);

        return $response = $this->_client->request();
    }

}
