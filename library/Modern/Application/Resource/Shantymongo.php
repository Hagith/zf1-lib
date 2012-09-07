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

/**
 * Shanty Mongo application resource.
 * @link https://github.com/coen-hyde/Shanty-Mongo
 *
 * @category    Modern
 * @package     Modern_Application
 * @subpackage  Resource
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Application_Resource_ShantyMongo extends Zend_Application_Resource_ResourceAbstract
{
    protected $_env = array(
        'MONGOLAB_URI',
    );

    public function init()
    {
        $connected = false;
        foreach ($this->_env as $env) {
            if (strlen(getenv($env))) {
                $conn = new Shanty_Mongo_Connection(getenv($env));
                Shanty_Mongo::addMaster($conn);
                $connected = true;
                break;
            }
        }

        if (!$connected) {
            Shanty_Mongo::addConnections($this->getOptions());
        }
    }

}
