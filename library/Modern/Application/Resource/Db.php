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

/** @see Zend_Application_Resource_Db */
require_once('Zend/Application/Resource/Db.php');

/**
 * This class is extension of Zend_Application_Resource_Db.
 * Allows to:
 * - enable db profiler
 * - setup metadata cache
 * - run queries eg. SET NAMES utf8
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */
class Modern_Application_Resource_Db extends Zend_Application_Resource_Db
{
    /**
     * Retrieve initialized DB connection
     *
     * @return null|Zend_Db_Adapter_Abstract
     */
    public function getDbAdapter()
    {
        $db = parent::getDbAdapter();
        if(null === $db) {
            return null;
        }

        $options = $this->getOptions();

        if(isset($options['tableMetadataCache']) && is_array($options['tableMetadataCache'])) {
            $cache = Zend_Cache::factory(
                $options['tableMetadataCache']['frontend']['name'],
                $options['tableMetadataCache']['backend']['name'],
                $options['tableMetadataCache']['frontend']['options'],
                $options['tableMetadataCache']['backend']['options']
            );
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }

        $options = $this->getOptions();

        // enable db profiler
        if(isset($options['profiler']) && $options['profiler']) {
            $db->getProfiler()->setEnabled(true);

            $bootstrap = $this->getBootstrap();
            $bootstrap->bootstrap('Log');
            $log = $bootstrap->getResource('Log');
            if ($log) {
                $log->info('Zend_Db_Profiler enabled!');
            }
        }

        // run startup db queries
        if(isset($options['queries']) && is_array($options['queries'])) {
            foreach ($options['queries'] as $query) {
                $db->query($query);
            }
        }

        return $db;
    }
}
