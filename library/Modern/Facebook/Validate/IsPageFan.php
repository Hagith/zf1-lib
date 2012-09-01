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
 * @package     Modern_Facebook
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Modern_Facebook_Validate_Abstract */
require_once 'Modern/Facebook/Validate/Abstract.php';

/**
 * Check logged user likes fanpage.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Validate_IsPageFan extends Modern_Facebook_Validate_Abstract
{
    const NO_PAGE_FAN = 'noPageFan';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NO_PAGE_FAN => "Polub profil %pageName%",
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array(
        'pageName' => '_pageName',
    );

    /**
     *
     * @var string
     */
    protected $_pageId;

    /**
     *
     * @var string
     */
    protected $_pageName;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'pageId' => string, profile pgae ID
     *   'pageName' => string, profile page name for message template
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp['pageId'] = array_shift($options);
            if (!empty($options)) {
                $temp['pageName'] = array_shift($options);
            }

            $options = $temp;
        }

        $this
            ->setPageId($options['pageId'])
            ->setPageName($options['pageName']);
    }

    public function getPageId()
    {
        return $this->_pageId;
    }

    /**
     *
     * @param string $id
     * @return Modern_Facebook_Validate_IsPageFan
     */
    public function setPageId($id)
    {
        $this->_pageId = $id;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getPageName()
    {
        return $this->_pageName;
    }

    /**
     *
     * @param boolean $flag
     * @return Modern_Facebook_Validate_IsPageFan
     */
    public function setPageName($name)
    {
        $this->_pageName = $name;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface.
     *
     * Return true if user liked profile page, false otherwise.
     *
     * @param string $value Logged Facebook user ID
     * @return boolean
     */
    public function isValid($value)
    {
        $result = $this->getFacebook()->fql("SELECT target_id FROM connection WHERE source_id = $value AND target_id = {$this->_pageId}");
        if (null == array_shift($result)) {
            $this->_error(self::NO_PAGE_FAN);
            return false;
        }
        return true;
    }

}
