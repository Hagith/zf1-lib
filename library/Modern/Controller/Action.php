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
 * @subpackage  Action
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Base action controller class.
 *
 * @category    Modern
 * @package     Modern_Controller
 * @subpackage  Action
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
abstract class Modern_Controller_Action extends Zend_Controller_Action
{
    /**
     * Bootstrap reference.
     *
     * @var Modern_Application_Bootstrap
     */
    protected $_bootstrap;

    /**
     * Logger reference.
     *
     * @var Modern_Log
     */
    protected $_log;

    /**
     * FlashMessenger controller helper instance.
     *
     * @var Modern_Controller_Action_Helper_FlashMessenger
     */
    protected $_messenger;

    /**
     * ContextSwitch controller helper instance
     *
     * @var Zend_Controller_Action_Helper_ContextSwitch
     */
    protected $_context;

    /**
     * Class constructor
     *
     * The request and response objects should be registered with the
     * controller, as should be any additional optional arguments; these will be
     * available via {@link getRequest()}, {@link getResponse()}, and
     * {@link getInvokeArgs()}, respectively.
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $response,
     * $invokeArgs).
     *
     * After the request, response, and invokeArgs are set, the
     * {@link $_helper helper broker} is initialized.
     *
     * Finally, {@link init()} is called as the final action of
     * instantiation, and may be safely overridden to perform initialization
     * tasks; as a general rule, override {@link init()} instead of the
     * constructor to customize an action controller's instantiation.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->setRequest($request)
             ->setResponse($response)
             ->_setInvokeArgs($invokeArgs);
        $this->_helper = new Zend_Controller_Action_HelperBroker($this);

        $this->_bootstrap = $this->getInvokeArg('bootstrap');
        $this->_log = $this->_bootstrap->getResource('log');

        $this->_messenger = $this->_helper->getHelper('flashMessenger');

        // initialize context helper
        $this->_context = $this->_helper->getHelper('contextSwitch');
        if(!$this->_context->hasContext('html')) {
            $this->_context->addContext(
                'html', array(
                    'suffix'    => '',
                    'headers'   => array('Content-Type' => 'text/html'),
                )
            );
        }
        $this->_context->setContextParam('context');
        $this->_context->setCallback('json', 'TRIGGER_INIT', array($this, 'contextJsonCallback'));
        $this->_context->setCallback('xml', 'TRIGGER_INIT', array($this, 'contextXmlCallback'));
        $this->_context->setCallback('html', 'TRIGGER_INIT', array($this, 'contextXmlCallback'));

        $this->init();

        $this->_context->initContext();
    }

    /**
     * Callback for JSON context.
     *
     * Turn off view renderer and layout.
     *
     */
    public function contextJsonCallback()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }

    /**
     * Callback for XML context.
     *
     * Turn off layout.
     *
     */
    public function contextXmlCallback()
    {
        $this->_helper->layout->disableLayout();
    }

}
