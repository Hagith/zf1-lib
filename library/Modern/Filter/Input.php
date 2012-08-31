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
 * @package     Modern_Filter
 * @subpackage  Input
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Zend_Filter_Input */
require_once 'Zend/Filter/Input.php';

/**
 * Extended Zend_Filter_Input class with ability to retreive filtered data
 * after checking the input data (after Zend_Filter_Input::isValid() call)
 *
 * @category    Modern
 * @package     Modern_Filter
 * @subpackage  Input
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Filter_Input extends Zend_Filter_Input
{
    /**
     * Filtered data.
     *
     * @var array
     */
    protected $_filtered;

    /**
     * Get filtered data.
     *
     * @return array
     */
    public function getFiltered()
    {
        return $this->_filtered;
    }

    /**
     * Perform data filtering.
     *
     */
    protected function _filter()
    {
        $this->_filtered = array();

        foreach ($this->_filterRules as $ruleName => &$filterRule) {
            /**
             * Make sure we have an array representing this filter chain.
             * Don't typecast to (array) because it might be a Zend_Filter object
             */
            if (!is_array($filterRule)) {
                $filterRule = array($filterRule);
            }

            /**
             * Filters are indexed by integer, metacommands are indexed by string.
             * Pick out the filters.
             */
            $filterList = array();
            foreach ($filterRule as $key => $value) {
                if (is_int($key)) {
                    $filterList[] = $value;
                }
            }

            /**
             * Use defaults for filter metacommands.
             */
            $filterRule[self::RULE] = $ruleName;
            if (!isset($filterRule[self::FIELDS])) {
                $filterRule[self::FIELDS] = $ruleName;
            }

            /**
             * Load all the filter classes and add them to the chain.
             */
            if (!isset($filterRule[self::FILTER_CHAIN])) {
                $filterRule[self::FILTER_CHAIN] = new Zend_Filter();
                foreach ($filterList as $filter) {
                    if (is_string($filter) || is_array($filter)) {
                        $filter = $this->_getFilter($filter);
                    }
                    $filterRule[self::FILTER_CHAIN]->addFilter($filter);
                }
            }

            /**
             * If the ruleName is the special wildcard rule,
             * then apply the filter chain to all input data.
             * Else just process the field named by the rule.
             */
            if ($ruleName == self::RULE_WILDCARD) {
                foreach (array_keys($this->_data) as $field) {
                    $this->_filterRule(array_merge($filterRule, array(self::FIELDS => $field)));
                }
            } else {
                $this->_filterRule($filterRule);
            }
        }
    }

    /**
     * @param array $filterRule
     * @return void
     */
    protected function _filterRule(array $filterRule)
    {
        $field = $filterRule[self::FIELDS];
        if (!array_key_exists($field, $this->_data)) {
            return;
        }
        if (is_array($this->_data[$field])) {
            foreach ($this->_data[$field] as $key => $value) {
                $this->_data[$field][$key] = $filterRule[self::FILTER_CHAIN]->filter($value);
                $this->_filtered[$field][$key] = $this->_data[$field][$key];
            }
        } else {
            $this->_data[$field] =
                $filterRule[self::FILTER_CHAIN]->filter($this->_data[$field]);
            $this->_filtered[$field] = $this->_data[$field];
        }
    }

}
