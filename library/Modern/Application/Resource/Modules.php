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
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Application_Resource_Modules */
require_once('Zend/Application/Resource/Modules.php');

/**
 * Module bootstrapping resource.
 *
 * Loads only current application modules defined in configuration.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_Modules extends Zend_Application_Resource_Modules
{
    /**
     * Base path to modules directory.
     *
     * @var string
     */
    protected $_basePath = '';

    /**
     * Current application name.
     *
     * @var string
     */
    protected $_applicationName;

    /**
     * Initialize modules.
     *
     * - Registers paths in Zend_Controller_Front.
     * - Adds autoloader namespaces for model classes.
     * - Loads routes.
     * - Loads module-specific bootstraps.
     *
     * @return array
     * @throws Zend_Application_Resource_Exception When bootstrap class was not found
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('FrontController');

        $front = $bootstrap->getResource('FrontController');
        $autoloader = $bootstrap->getApplication()->getAutoloader();
        $this->_applicationName = $bootstrap->getApplication()->getName();
        $curBootstrapClass = get_class($bootstrap);
        $defaultModule = $front->getDefaultModule();

        $options = $this->getOptions();
        if(isset($options['basepath'])) {
            $this->_basePath = rtrim($options['basepath'], '/') . '/';
        }
        $modules = isset($options['module']) ? (array)$options['module'] : array();

        foreach($modules as $module => $enabled) {
            if(!$enabled) {
                continue;
            }
            $module = $this->_formatModuleName($module);

            $moduleDir = "{$this->_basePath}$module/apps/{$this->_applicationName}/{$front->getModuleControllerDirectoryName()}";
            $front->addControllerDirectory($moduleDir, strtolower($module));

            $autoloader->registerNamespace($module . '_');

            $routes = $this->_getModuleRoutes($module);
            if($routes instanceof Zend_Config) {
                $front->getRouter()->addConfig($routes);
            }

            // Load module bootstrap - if exists
            $bootstrapClass = $module . '_Model_Bootstrap';
            if (!class_exists($bootstrapClass, false)) {
                $bootstrapPath  = "{$this->_basePath}$module/Model/Bootstrap.php";
                if (!file_exists($bootstrapPath)) {
                    continue;
                }
                $eMsgTpl = 'Bootstrap file found for module "%s" but bootstrap class "%s" not found';
                include_once $bootstrapPath;
                if (($defaultModule != strtolower($module))
                    && !class_exists($bootstrapClass, false)
                ) {
                    throw new Zend_Application_Resource_Exception(sprintf(
                        $eMsgTpl, $module, $bootstrapClass
                    ));
                } elseif ($defaultModule == strtolower($module)) {
                    if (!class_exists($bootstrapClass, false)) {
                        $bootstrapClass = 'Bootstrap';
                        if (!class_exists($bootstrapClass, false)) {
                            throw new Zend_Application_Resource_Exception(sprintf(
                                $eMsgTpl, $module, $bootstrapClass
                            ));
                        }
                    }
                }
            }

            if ($bootstrapClass == $curBootstrapClass) {
                // If the found bootstrap class matches the one calling this
                // resource, don't re-execute.
                continue;
            }

            $moduleBootstrap = new $bootstrapClass($bootstrap->getApplication());
            $moduleBootstrap->bootstrap();
            $this->_bootstraps[$module] = $moduleBootstrap;
        }

        return $this->_bootstraps;
    }

    /**
     * Get routes configuration for module.
     *
     * @param string $module
     * @return Zend_Config|null
     */
    protected function _getModuleRoutes($module)
    {
        $routes = "{$this->_basePath}$module/configs/routes.ini";
        if(!file_exists($routes)) {
            return null;
        }

        $routes = new Zend_Config_Ini($routes, $this->getBootstrap()->getEnvironment());
        $routes = $routes->{$this->_applicationName};
        if(!$routes instanceof Zend_Config) {
            return null;
        }

        $routes = $routes->toArray();

        foreach ($routes as $name => $route) {
            $routes[strtolower($module) . '.' . $name] = $route;
            unset($routes[$name]);
        }

        return new Zend_Config($routes);
    }

}
