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
 * @subpackage  Dispatcher
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Controller_Dispatcher_Standard */
require_once('Zend/Controller/Dispatcher/Standard.php');

/**
 * Base dispatcher class.
 *
 * Modifies default naming standard of action controllers to contain
 * current application name, eg. News_Website_ListController.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Dispatcher
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Standard
{
    /**
     * Format action class name.
     *
     * @param string $moduleName
     * @param string $className
     * @return string
     */
    public function formatClassName($moduleName, $className)
    {
        $applicationName = $this->getParam('bootstrap')->getApplication()->getName();
        return $this->formatModuleName($moduleName) . '_' .
            $this->formatApplicationName($applicationName) . '_' . $className
        ;
    }

    /**
     * Format the application name.
     *
     * @param string $unformatted
     * @return string
     */
    public function formatApplicationName($unformatted)
    {
        return ucfirst($this->_formatName($unformatted));
    }
}