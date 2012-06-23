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

/** @see Zend_Controller_Action_Helper_Abstract */
require_once('Zend/Controller/Action/Helper/Abstract.php');

/**
 * Helper to track user's path history.
 *
 * @category    Modern
 * @package     Modern_Controller
 * @package     Action_Helper
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Controller_Action_Helper_Breadcrumbs extends Zend_Controller_Action_Helper_Abstract implements IteratorAggregate, Countable
{
    /**
     * Default session namespace to store breadcrumbs
     */
    const DEFAULT_SESSION_NAMESPACE = 'Modern_Breadcrumbs';

    /**
     * Zend session namespace to store breadcrumbs.
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * Session container name that holds user's path.
     *
     * @var string
     */
    protected $_container = 'history';

    /**
     * Session container name that holds user's path markers.
     *
     * @var string
     */
    protected $_markers = 'markers';

    /**
     * Max histoty length.
     *
     * @var integer
     */
    protected $_maxLength = 10;

    /**
     * @var boolean
     */
    private $_dispached = false;

    /**
     * @param string $namespace
     */
    public function __construct($namespace = null)
    {
        if (null === $namespace) {
            $namespace = self::DEFAULT_SESSION_NAMESPACE;
        }
        $this->_session = new Zend_Session_Namespace($namespace);
    }

    /**
     * Hook into action controller preDispatch() workflow
     *
     */
    public function preDispatch()
    {
        if ($this->_dispached) {
            return;
        }

        $request = $this->getRequest();

        if ($request->isPost() || $request->isXmlHttpRequest()) {
            return;
        }

        $url = '';
        $url .= $request->getPathInfo();

        $query = $request->getQuery();
        if (count($query)) {
            $url .= '?' . urldecode(http_build_query($query));
        }

        $this->addStep($url);
        $this->_dispached = true;
    }

    /**
     * Add url to history.
     *
     * @param string $url
     * @return Modern_Controller_Action_Helper_Breadcrumbs
     */
    public function addStep($url)
    {
        if (!$this->hasSteps()) {
            $this->_session->{$this->_container} = array();
        }

        // omit refreshing page
        if ($url == end($this->_session->{$this->_container})) {
            return $this;
        }

        $this->_session->{$this->_container}[] = $url;

        if ($this->count() > $this->_maxLength) {
            array_shift($this->_session->{$this->_container});
        }

        return $this;
    }

    /**
     * Get url from history.
     *
     * Offset calculated from the current user position must be integer <= 0.
     *
     * @param integer $offset
     * @return string
     * @throws Modern_Controller_Action_Helper_Exception
     *  If step with given offset doesn't exist.
     */
    public function getStep($offset)
    {
        if (!$this->hasStep($offset)) {
            /** @see Modern_Controller_Action_Helper_Exception */
            require_once('Modern/Controller/Action/Helper/Exception.php');
            throw new Modern_Controller_Action_Helper_Exception(
                "Given offset '$offset' doesn't exist"
            );
        }

        $offset--;
        return ($this->_session->{$this->_container}[$this->count() + $offset]);
    }

    /**
     * Returns whether there is a step at the specified index.
     *
     * Offset calculated from the current user position must be integer <= 0.
     *
     * @param integer $offset
     * @return boolean
     * @throws Modern_Controller_Action_Helper_Exception
     *  If offset is greater than zero.
     */
    public function hasStep($offset)
    {
        if ($offset > 0) {
            /** @see Modern_Controller_Action_Helper_Exception */
            require_once('Modern/Controller/Action/Helper/Exception.php');
            throw new Modern_Controller_Action_Helper_Exception(
                "Offset cannot be greater than zero"
            );
        }

        if (!$this->hasSteps()) {
            return false;
        }

        $offset--;
        return isset($this->_session->{$this->_container}[$this->count() + $offset]);
    }

    /**
     * Returns whether the object contains at least one step.
     *
     * @return boolean
     */
    public function hasSteps()
    {
        return
            is_array($this->_session->{$this->_container})
            && count($this->_session->{$this->_container});
    }

    /**
     * Returns an array of steps.
     *
     * @return array
     */
    public function getSteps()
    {
        if ($this->hasSteps()) {
            return $this->_session->{$this->_container};
        }

        return array();
    }

    /**
     * Clears the container that contains the steps.
     *
     * @return Modern_Controller_Action_Helper_Breadcrumbs
     */
    public function clear()
    {
        $this->_session->{$this->_container} = array();

        return $this;
    }

    /**
     * Sets the maximum recorded length of the user's path.
     *
     * @param int $length
     * @return Modern_Controller_Action_Helper_Breadcrumbs
     * @throws Modern_Controller_Action_Helper_Exception If length is less than 2.
     */
    public function setMaxLength($length)
    {
        if ((int) $length < 2) {
            /** @see Modern_Controller_Action_Helper_Exception */
            require_once('Modern/Controller/Action/Helper/Exception.php');
            throw new Modern_Controller_Action_Helper_Exception(
                "The maximum length of the user's path must not be less than 2"
            );
        }
        $this->_maxLength = (int) $length;

        $count = $this->count();
        if ($count > $this->_maxLength) {
            $this->_session->{$this->_container} = array_slice(
                $this->_session->{$this->_container}, $count - $this->_maxLength, $count - 1
            );
        }

        return $this;
    }

    /**
     * Returns the current maximum length of the user's path.
     *
     * @return integer
     */
    public function getMaxLength()
    {
        return $this->_maxLength;
    }

    /**
     * Sets the marker with the specified name for the specified $offset step
     * through the user's path.
     *
     * @param string $markerName
     * @param integer $offset
     * @return Modern_Controller_Action_Helper_Breadcrumbs
     */
    public function setMarked($markerName, $offset)
    {
        $this->_session->{$this->_markers}[$markerName] = $this->getStep($offset);

        return $this;
    }

    /**
     * Checks if there is a marker with the specified name.
     *
     * @param string $markerName
     * @return boolean
     */
    public function hasMarked($markerName)
    {
        return isset($this->_markers[$markerName]);
    }

    /**
     * Returns the address indicated by the $markerName.
     *
     * @param string $markerName
     * @return string
     * @throws Modern_Controller_Action_Helper_Exception
     *  If the marker with the specified name does not exist.
     */
    public function getMarked($markerName)
    {
        if (!$this->hasMarked($markerName)) {
            /** @see Modern_Controller_Action_Helper_Exception */
            require_once('Modern/Controller/Action/Helper/Exception.php');
            throw new Modern_Controller_Action_Helper_Exception(
                "Marker with name '$markerName' does not exist"
            );
        }

        return $this->_session->{$this->_markers}[$markerName];
    }

    /**
     * Implementation of interface IteratorAggregate.
     *
     * Returns an iterator object that contains the steps.
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject($this->getSteps());
    }

    /**
     * Implementation of interface Countable.
     *
     * Returns the length of the registered user's path.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->hasSteps()) {
            return count($this->_session->{$this->_container});
        }

        return 0;
    }

}
