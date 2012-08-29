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
 * Rozszerzenie zasobu widoku obsługujące większą ilość opcji konfiguracyjnych.
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2011 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_View extends Zend_Application_Resource_View {

    /**
     * Retrieve view object
     *
     * @return Modern_View
     */
    public function getView() {
        if (null === $this->_view) {
            $options = $this->getOptions();
            $this->_view = new Modern_View($options);

            if (isset($options['doctype'])) {
                $this->_view->doctype()->setDoctype(strtoupper($options['doctype']));
                if (isset($options['charset']) && $this->_view->doctype()->isHtml5()) {
                    $this->_view->headMeta()->setCharset($options['charset']);
                }
            }
            if (isset($options['contentType'])) {
                $this->_view->headMeta()->appendHttpEquiv('Content-Type', $options['contentType']);
            }
            if (isset($options['assign']) && is_array($options['assign'])) {
                $this->_view->assign($options['assign']);
            }

            // ustawienie dodatkowych ścieżek do helperów
            if (isset($options['helperPaths']) && is_array($options['helperPaths'])) {
                foreach ($options['helperPaths'] as $prefix => $path) {
                    $this->_view->addHelperPath($path, $prefix);
                }
            }

            // konfiguracja helperów widoku
            if (isset($options['helper']) && is_array($options['helper'])) {
                $this->_setupHelpers($options['helper']);
            }
        }

        return $this->_view;
    }

    /**
     * Konfiguruje helpery widoku.
     *
     * @param array $options
     */
    protected function _setupHelpers(array $options) {
        foreach ($options as $helperName => $config) {
            $helper = $this->_view->getHelper($helperName);
            if(method_exists($helper, 'setOptions')) {
                $helper->setOptions($config);
            }
        }
    }

}
