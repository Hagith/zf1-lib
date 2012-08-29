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
 * @package     Modern_Mail
 * @subpackage  Transport
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Mail_Transport_Smtp */
require_once('Zend/Mail/Transport/Smtp.php');

/**
 * Klasa do obsługi wysyłki maili poprzez SMTP.
 * Nadpisuje Zend_Mail_Transport_Smtp ponieważ wymagane jest pobieranie z niej konfiguracji.
 *
 * @category    Modern
 * @package     Modern_Mail
 * @subpackage  Transport
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Mail_Transport_Smtp extends Zend_Mail_Transport_Smtp
{
    /**
     * Metoda wysyłająca e-mail przez SMTP. Jeśli nie zdefiniowano wcześniej nadawcy,
     * a znajduje się domyślny w konfiguracji, to staje się on nadawcą wysyłanego e-maila.
     *
     * @param Zend_Mail $mail
     */
    public function send(Zend_Mail $mail)
    {
        if(null === $mail->getFrom()) {
            if(!isset($this->_config['fromName'])) {
                $this->_config['fromName'] = '';
            }
            if(isset($this->_config['fromEmail']) && strlen($this->_config['fromEmail']) > 0) {
                $mail->setFrom($this->_config['fromEmail'], $this->_config['fromName']);
            }
        }

        parent::send($mail);
    }

}
