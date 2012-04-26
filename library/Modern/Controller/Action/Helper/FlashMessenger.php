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
 * @package     Modern_Controller
 * @package     Action_Helper
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Controller_Action_Helper_FlashMessenger */
require_once('Zend/Controller/Action/Helper/FlashMessenger.php');

/**
 * Extension of base ZF FlashMessenger controller helper.
 * {@see http://framework.zend.com/manual/en/zend.controller.actionhelpers.html#zend.controller.actionhelpers.flashmessenger}
 *
 * Flash message extended with type (success, info, warning, error).
 *
 * @category    Modern
 * @package     Modern_Controller
 * @package     Action_Helper
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Controller_Action_Helper_FlashMessenger extends Zend_Controller_Action_Helper_FlashMessenger
{
    /**
     * Message types.
     */
    const MESSAGE_SUCCESS   = 'success';
    const MESSAGE_INFO      = 'info';
    const MESSAGE_WARNING   = 'warning';
    const MESSAGE_ERROR     = 'error';

    /**
     * Message types array.
     *
     * @var array
     */
    protected $_types = array();

    /**
     * Setup available message types from class constants.
     *
     */
    public function __construct() {
        parent::__construct();

        $ref = new ReflectionObject($this);
        $this->_types = $ref->getConstants();
    }

    /**
     * Add flash message with specified type.
     *
     * @param string $message
     * @param integer $type
     * @return Modern_Controller_Action_Helper_Messenger
     */
    public function addMessage($message, $type = self::MESSAGE_SUCCESS)
    {
        if (self::$_messageAdded === false) {
            self::$_session->setExpirationHops(1, null, true);
        }

        if (!is_array(self::$_session->{$this->_namespace})) {
            self::$_session->{$this->_namespace} = array();
        }

        if(!in_array($type, $this->_types)) {
            throw new Modern_Controller_Action_Helper_Exception(
                "Uknown message type '$type'"
            );
        }

        $message = array(
            'type'    => $type,
            'body'    => $message,
        );

        self::$_session->{$this->_namespace}[] = $message;

        return $this;
    }

}
