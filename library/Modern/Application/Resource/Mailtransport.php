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
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Setup default mail transport.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_MailTransport extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Modern_Mail_Transport_Smtp
     */
    protected $_mailTransport;

    /**
     * Inicjacja komponentu Zend_Log.
     *
     * @return Modern_Mail_Transport_Smtp
     */
    public function init()
    {
        return $this->getMailTransport();
    }

    /**
     * Zwraca obiekt Modern_Mail_Transport_Smtp.
     *
     * @return Modern_Mail_Transport_Smtp
     */
    public function getMailTransport()
    {
        if (null === $this->_mailTransport) {

            $options = $this->getOptions();

            if(isset($options['host'])) {
                $this->_mailTransport = new Modern_Mail_Transport_Smtp($options['host'], $options);
                Modern_Mail::setDefaultTransport($this->_mailTransport);
            } else {
                throw new Modern_Application_Resource_Exception("Brak konfiguracji 'host' dla transportu SMTP");
            }
        }
        return $this->_mailTransport;
    }

}
