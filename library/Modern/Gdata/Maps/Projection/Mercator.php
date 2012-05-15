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
class Modern_Gdata_Maps_Projection_Mercator
{
    const TILE_SIZE = 256;

    /**
     * @var Modern_Gdata_Maps_Point
     */
    protected $_pixelOrigin;

    /**
     * @var float
     */
    protected $_pixelsPerLonDegree;

    /**
     * @var float
     */
    protected $_pixelsPerLonRadian;

    /**
     * @param array|Zend_Config $options
     */
    public function __construct($options = array())
    {
        $this->_pixelOrigin = new Modern_Gdata_Maps_Point(
            self::TILE_SIZE / 2,
            self::TILE_SIZE / 2
        );
        $this->_pixelsPerLonDegree = self::TILE_SIZE / 360;
        $this->_pixelsPerLonRadian = self::TILE_SIZE / (2 * M_PI);
    }

    /**
     * Ustawia opcje konfiguracyjne.
     *
     * @param array|Zend_Config $options
     * @return Model_Mosaic
     */
    public function setOptions($options)
    {
        if($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        foreach($this->_options as $key => $value) {
            if(isset($options[$key])) {
                $this->_options[$key] = $options[$key];
            }
        }
        return $this;
    }

    public function fromLatLngToPoint($latLng, Modern_Gdata_Maps_Point $point = null)
    {
        if(!$point) {
            $point = new Modern_Gdata_Maps_Point(0, 0);
        }
        $origin = $this->_pixelOrigin;

        $point->x = $origin->x + $latLng->lng() * $this->_pixelsPerLonDegree;

        // NOTE(appleton): Truncating to 0.9999 effectively limits latitude to
        // 89.189.  This is about a third of a tile past the edge of the world
        // tile.
        $siny = $this->_getBound(sin($this->_degreesToRadians($latLng->lat())), -0.9999, 0.9999);
        $point->y = $origin->y + 0.5 * log((1 + $siny) / (1 - $siny)) * -$this->_pixelsPerLonRadian;
        return $point;
    }

    /**
     * @param Modern_Gdata_Maps_Point $point
     * @return Modern_Gdata_Maps_LatLng
     */
    public function fromPointToLatLng(Modern_Gdata_Maps_Point $point)
    {
        $origin = $this->_pixelOrigin;
        $lng = ($point->x - $origin->x) / $this->_pixelsPerLonDegree;
        $latRadians = ($point->y - $origin->y) / -$this->_pixelsPerLonRadian;
        $lat = $this->_radiansToDegrees(2 * atan(exp($latRadians)) - M_PI / 2);
        return new Modern_Gdata_Maps_LatLng($lat, $lng);
    }

    /**
     * @param Modern_Gdata_Maps_Point $pixel
     * @param integer $zoom
     * @return Modern_Gdata_Maps_LatLng
     */
    public function fromPixelToLatLng(Modern_Gdata_Maps_Point $pixel, $zoom)
    {
        $numTiles = 1 << $zoom;
        $worldCoord = new Modern_Gdata_Maps_Point(
            $pixel->x / $numTiles,
            $pixel->y / $numTiles
        );

        return $this->fromPointToLatLng($worldCoord);
    }

    protected function _getBound($value, $min = null, $max = null)
    {
        if ($min != null) $value = max($value, $min);
        if ($max != null) $value = min($value, $max);
        return $value;
    }

    protected function _degreesToRadians($deg)
    {
        return $deg * (M_PI / 180);
    }

    protected function _radiansToDegrees($rad)
    {
        return $rad / (M_PI / 180);
    }

}