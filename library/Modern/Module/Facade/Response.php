<?php
/**
 * Modern
 *
 * LICENSE
 *
 * This source file is subject to version 1.0
 * of the ModernWeb license.
 *
 * @category    Modern
 * @package     Modern_Module
 * @subpackage  Facade
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/**
 * Klasa odpowiedzi dla fasady modelu.
 * Stanowi element wynikowy możliwych do wykonania operacji.
 *
 * @category    Modern
 * @package     Modern_Module
 * @subpackage  Facade
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Module_Facade_Response
{
    /**
     * Status odpowiedzi.
     *
     * @var boolean
     */
    protected $_status;

    /**
     * Komunikat opisujący status odpowiedzi.
     *
     * @var string
     */
    protected $_statusMessage;

    /**
     * Tablica komunikatów walidacji.
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Powołuje nowy obiekt odpowiedzi.
     * Umożliwia ustawienie początkowego statusu i komunikatu.
     *
     * @param boolean $status
     * @param string $message
     */
    public function __construct($status = false, $message = null)
    {
        $this->setStatus($status);
        if(null !== $message) {
            $this->_statusMessage = $message;
        }
    }

    /**
     * Ustawia ststus odpowiedzi.
     *
     * @param boolean $status
     * @return Modern_Module_Facade_Response
     */
    public function setStatus($status)
    {
        $this->_status = (boolean)$status;
        return $this;
    }

    /**
     * Zwraca status odpowiedzi.
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Ustawia komunikat opisowy statusu odpowiedzi.
     *
     * @param string $message
     * @return Modern_Module_Facade_Response
     */
    public function setStatusMessage($message)
    {
        $this->_statusMessage = $message;
        return $this;
    }

    /**
     * Zwraca komunikat opisowy statusu odpowiedzi.
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->_statusMessage;
    }

    /**
     * Ustawia komunikat dla pola.
     *
     * @param string $field
     * @param string $message
     * @return Modern_Module_Facade_Response
     */
    public function setMessage($field, $message)
    {
        $this->_messages[$field] = $message;
        return $this;
    }

    /**
     * Zwraca komunikat dla pola.
     *
     * @param string $field
     * @return string
     */
    public function getMessage($field)
    {
        return $this->_messages[$field];
    }

    /**
     * Zwraca tablicę komuniktów.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Zwraca informację czy obiekt odpowiedzi zawiera komunikaty.
     *
     * @return boolean
     */
    public function hasMessages()
    {
        return count($this->_messages) > 0;
    }

    /**
     * Zwraca informację o tym czy odpowiedź zawiera wykonane operacji.
     *
     * @return boolean
     */
    public function hasError()
    {
        return !$this->_status || $this->hasMessages();
    }
}