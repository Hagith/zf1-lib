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
 * @package     Modern_Log
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Log */
require_once 'Zend/Log.php';

/**
 * @category    Modern
 * @package     Modern_Log
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Log extends Zend_Log
{
    /**
     * Array for execution measurment timers.
     *
     * @var array
     */
    protected $_timers = array();

    /**
     * Overrided only becouse of wrong "new self" context.
     *
     * @param array|Zend_Config Array or instance of Zend_Config
     * @return Zend_Log
     * @throws Zend_Log_Exception
     */
    static public function factory($config = array())
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config) || empty($config)) {
            /** @see Zend_Log_Exception */
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
        }

        $log = new self;

        if (array_key_exists('timestampFormat', $config)) {
            if (null != $config['timestampFormat'] && '' != $config['timestampFormat']) {
                $log->setTimestampFormat($config['timestampFormat']);
            }
            unset($config['timestampFormat']);
        }

        if (!is_array(current($config))) {
            $log->addWriter(current($config));
        } else {
            foreach ($config as $writer) {
                $log->addWriter($writer);
            }
        }

        return $log;
    }

    /**
     * Log exception.
     *
     * @param Exception $e
     * @param integer $priority
     * @param array $extras
     */
    public function addException(Exception $e, $priority = self::CRIT, $extras = null)
    {
        $info = array('file' => $e->getFile(), 'line' => $e->getLine());
        if (is_array($extras)) {
            $info = array_merge($info, $extras);
        }
        $this->log($e->getMessage(), $priority, $info);
    }

    /**
     * Measure execution time.
     *
     * First call with new $namespace will only save current timestamp.
     * Another calls will log measured execution time from previous call.
     *
     * @param string $namespace
     * @return string
     */
    public function timer($namespace)
    {
        $time = null;

        if (isset($this->_timers[$namespace])) {
            $time = sprintf('%.4f', microtime(true) - $this->_timers[$namespace]);
            $this->info(sprintf("Execution time '%s': %s",
                $namespace, $time
            ));
        }

        $this->_timers[$namespace] = microtime(true);

        return $time;
    }

    /**
     * Packs message and priority into Event array.
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return array Event array
     */
    protected function _packEvent($message, $priority)
    {
        $event = parent::_packEvent($message, $priority);

        // try to read file/line info using debug_backtrace
        if (function_exists('debug_backtrace') && !isset($event['file']) && !isset($event['line'])) {
            $backtrace = debug_backtrace();
            for ($index = count($backtrace) - 1; $index >= 0; $index--) {
                if (isset($backtrace[$index]['class']) && $backtrace[$index]['class'] == __CLASS__) {
                    $backtrace = $backtrace[$index];
                    break;
                }
            }
            $event['file'] = isset($backtrace['file']) ? $backtrace['file'] : null;
            $event['line'] = isset($backtrace['line']) ? $backtrace['line'] : null;
        }

        return $event;
    }

}
