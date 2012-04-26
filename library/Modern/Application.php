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
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Application */
require_once('Zend/Application.php');

/**
 * Zend_Application component extension.
 *
 * Introduces a system breakdown to more than one application.
 *
 * @category    Modern
 * @package     Modern_Application
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application extends Zend_Application
{
    /**
     * Name of current application.
     *
     * @var string
     */
    protected $_name;

    /**
     * Overloaded constructor allows to specify the name of the application to run.
     *
     * @param string $environment
     * @param string|array|Zend_Config $options
     * @param string $name
     * @throws Zend_Application_Exception
     */
    public function __construct($environment, $options = null, $name = null)
    {
        if(null !== $name) {
            $this->setName($name);
        }
        parent::__construct($environment, $options);
    }

    /**
     * Set an application name.
     *
     * @param string $name
     * @return Modern_Application
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get the current application name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set application options.
     *
     * Overrides the parent method to handle the application-specific
     * configuration - if it has been set.
     *
     * @param array $options
     * @return Zend_Application
     */
    public function setOptions(array $options)
    {
        if (!empty($options['config'])) {
            $options = $this->mergeOptions($options, $this->_loadConfig($options['config']));
            unset($options['config']);
        }

        if(
            null !== $this->_name
            && !isset($options['applications'])
            && !is_array($options['applications'])
        ) {
            require_once 'Zend/Application/Exception.php';
            throw new Zend_Application_Exception("Nie określono listy dostępnych aplikacji");
        }

        // merge application-specific configuration
        foreach($options as $key => &$config) {
            if(null !== $this->_name && isset($config[$this->_name])) {
                $config = $this->mergeOptions($config, $config[$this->_name]);
            }
        }

        // remove other application-specific options
        foreach($options['applications'] as $application) {
            foreach ($options as $key => &$config) {
                if(isset($config[$application])) {
                    unset($config[$application]);
                }
            }
        }

        return parent::setOptions($options);
    }
}