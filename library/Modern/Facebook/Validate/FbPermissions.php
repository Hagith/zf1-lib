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
 * Check user permissions.
 *
 * @category    Modern
 * @package     Modern_Facebook
 * @subpackage  Validate
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Facebook_Validate_FbPermissions extends Modern_Facebook_Validate_Abstract
{
    const USE_CONFIG_PERMS = 'useConfig';
    const INSUFFICIENT_PERMISSIONS = 'insufficientPermissions';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INSUFFICIENT_PERMISSIONS => "Nie udzielono wymaganych uprawnień aplikacji",
    );

    /**
     *
     * @var array
     */
    protected $_perms = array();

    /**
     *
     * @var boolean
     */
    protected $_redirect = false;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'perms' => string|array of extended permissions
     *   'redirect' => boolean, redirect to permission page on validation failure
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
            $temp['perms'] = array_shift($options);
            if (!empty($options)) {
                $temp['redirect'] = array_shift($options);
            }

            $options = $temp;
        }

        $this
            ->setPerms($options['perms'])
            ->setRedirect($options['redirect']);
    }

    public function getPerms()
    {
        return $this->_perms;
    }

    /**
     *
     * @param string|array $perms
     * @return Modern_Facebook_Validate_FbPermissions
     */
    public function setPerms($perms)
    {
        if ($perms == self::USE_CONFIG_PERMS) {
            $this->_perms = $this->getFacebook()->getOption('permissions');
            return $this;
        }
        if (is_array($perms)) {
            $this->_perms = $perms;
            return $this;
        }

        $this->_perms = preg_split('/|,|/', (string) $perms, -1, PREG_SPLIT_NO_EMPTY);
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function getRedirect()
    {
        return $this->_redirec;
    }

    /**
     *
     * @param boolean $flag
     * @return Modern_Facebook_Validate_FbPermissions
     */
    public function setRedirect($flag)
    {
        $this->_redirect = (bool) $flag;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface.
     *
     * Return true if user granted all of required permissions.
     * If redirect option is true user is redirected to permission
     * page on validation failure, returns false otherwise.
     *
     * @param string $value Logged Facebook user ID
     * @return boolean
     */
    public function isValid($value)
    {
        $permissions = $this->getFacebook()->getLoggedUser()->getPermissions();
        foreach ($this->_perms as $perm) {
            if (!$permissions->hasUserPermission($perm)) {
                $this->_error(self::INSUFFICIENT_PERMISSIONS);

                if ($this->_redirect) {
                    $this->getFacebook()->accessRedirect();
                }
                return false;
            }
        }
        return true;
    }

}
