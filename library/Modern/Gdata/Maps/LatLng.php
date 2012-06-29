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
 * @package     Modern_Gdata
 * @package     Maps
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * @category    Modern
 * @package     Modern_Gdata
 * @package     Maps
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Gdata_Maps_LatLng
{
    protected $_lat = 0;
    protected $_lng = 0;

    /**
     * @param float $lat
     * @param float $lng
     * @param boolean $noWrap
     *
     * @todo If the noWrap flag is true, then the numbers will be used
     * as passed, otherwise latitude will be clamped to lie between
     * -90 degrees and +90 degrees, and longitude will be wrapped
     * to lie between -180 degrees and +180 degrees.
     */
    public function __construct($lat, $lng, $noWrap = false)
    {
        if ($noWrap) {
            $this->_lat = (float) $lat;
            $this->_lng = (float) $lng;
        } else {
            $this->_lat = (float) $lat;
            $this->_lng = (float) $lng;
        }
    }

    public function lat()
    {
        return $this->_lat;
    }

    public function lng()
    {
        return $this->_lng;
    }

    public function toUrlValue($precision = 6)
    {
        return sprintf("%.{$precision}f,%.{$precision}f", $this->_lat, $this->_lng);
    }

    public function toStrign($precision = null)
    {
        if (null === $precision) {
            $precision = 14;
        }
        return sprintf("(%s)", $this->toUrlValue($precision));
    }

    public function __toString()
    {
        return $this->toStrign();
    }

}
