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
 * @package     Modern_Image
 * @subpackage  Adapter
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/** @see Modern_Image_Adapter */
require_once 'Modern/Image/Adapter.php';

/**
 * GD2 image adapter implementation.
 *
 * @category    Modern
 * @package     Modern_Image
 * @subpackage  Adapter
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Image_Adapter_Gd2 extends Modern_Image_Adapter
{
    /**
     * Tablica stałych typów plików dla GD2
     *
     * @var array
     */
    private $_imageTypes = array(
        Modern_Image_Adapter::PNG => IMG_PNG,
        Modern_Image_Adapter::JPG => IMG_JPG,
        Modern_Image_Adapter::GIF => IMG_GIF,
    );

    /**
     * Zapisanie informacji o kanale alpha dla podanego zasobu
     *
     * @param Modern_Image_Adapter
     */
    private function _saveAlphaData($resource)
    {
        imagealphablending($resource, false);
        imagesavealpha($resource, true);
    }

    /**
     * Zniszczenie zasobu obrazu, jeśli istnieje
     *
     */
    protected function _destroyImage()
    {
        if ($this->_resource) {
            imagedestroy($this->_resource);
        }
    }

    /**
     * Metoda konwertująca procentowa wartość jakości zapisu obrazu
     * na wartość stopnia kompresji obrazu PNG.
     *
     * @param integer      Wartość jakości zapisu obrazu (zakres: 0-100)
     * @return integer     Wartość stopnia kompresji PNG (zakres: 0-9)
     */
    protected function _qualityToPngCompress($value)
    {
        return abs((round($value / 11.11)) - 9);
    }

    /**
     * Pobranie obsługiwanych typów plików graficznych
     *
     * @return array        Tablica obsługiwanych typów plików graficznych
     */
    protected function _getSupportedTypes()
    {
        $supported = array();

        foreach ($this->_imageTypes as $index => $type) {
            if (imagetypes() & $type) {
                $supported[] = $index;
            }
        }

        return $supported;
    }

    /**
     * Metoda realizująca przekształcenie zasobu obrazu do określonych wymiarów
     *
     * @param integer $width  Szerokość uzyskana z obliczeń w metodach resize() i resizeTo()
     * @param integer $height Wysokość uzyskana z obliczeń w metodach resize() i resizeTo()
     */
    protected function _resize($width, $height)
    {
        // Utworzenie tymczasowego zasobu
        $tmpRes = imagecreatetruecolor($width, $height);
        // Zapisanie danych kanału alpha dla tymczasowego zasobu
        if ($this->_config->imageType == Modern_Image_Adapter::PNG) {
            $this->_saveAlphaData($tmpRes);
        }
        // Skopiowanie do tymczasowego zasobu (o nowych wymiarach) zawartosci oryginalnego obrazu
        $controll = imagecopyresampled(
            $tmpRes, $this->_resource,
            0, 0, 0, 0,
            $width, $height,
            $this->_size[Modern_Image_Adapter::WIDTH],
            $this->_size[Modern_Image_Adapter::HEIGHT]
        );
        if ($controll) {
            imagedestroy($this->_resource);
            $this->_resource = $tmpRes;
        } else {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Błąd podczas przetwarzania zasobu obrazu.');
        }
    }

    /**
     * Metoda realizująca przetworzenie zasobu obrazu do określonych wymiarów
     *
     * @param integer $startX Współrzędna X lewego górnego rogu wycinanego elementu
     * @param integer $startY Współrzędna Y lewego górnego rogu wycinanego elementu
     * @param integer $width  Szerokość wycinanego elementu
     * @param integer $height Wysokość wycinanego elementu
     */
    protected function _crop($startX, $startY, $width, $height)
    {
        // Utworzenie tymczasowego zasobu o docelowych wymiarach
        $tmpRes = imagecreatetruecolor($width, $height);
        // Zapisanie danych kanału alpha dla tymczasowego zasobu
        if ($this->_config->imageType == Modern_Image_Adapter::PNG) {
            $this->_saveAlphaData($tmpRes);
        }
        // Skopiowanie do tymczasowego zasobu wycietego fragmentu oryginalnego obrazka
        $controll = imagecopyresampled(
            $tmpRes, $this->_resource,
            0, 0, $startX, $startY,
            $width, $height,
            $width, $height
        );
        if ($controll) {
            imagedestroy($this->_resource);
            $this->_resource = $tmpRes;
        } else {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Błąd podczas przetwarzania zasobu obrazu.');
        }
    }

    /**
     * Ustawienie wewnętrznej tablicy wymiarów obrazu na wartości aktualne
     *
     * @param integer|null         Szerokość
     * @param integer|null         Wysokość
     */
    protected function _setSize($width=null, $height=null)
    {
        if (null === $width) {
            $this->_size[Modern_Image_Adapter::WIDTH] = imagesx($this->_resource);
        } else {
            $this->_size[Modern_Image_Adapter::WIDTH] = (int) $width;
        }
        if (null === $height) {
            $this->_size[Modern_Image_Adapter::HEIGHT] = imagesy($this->_resource);
        } else {
            $this->_size[Modern_Image_Adapter::HEIGHT] = (int) $height;
        }
    }

    /**
     * Utworzenie nowego zasobu obrazu
     *
     * @param integer $width        Szerokość obrazu
     * @param integer $height       Wysokość obrazu
     * @return Modern_Image_Adapter Zwraca referencję do adaptera ($this)
     */
    public function create($width, $height)
    {
        $width = (int) $width;
        $height = (int) $height;

        if ($width <= 0 || $height <= 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception(
                'Szerokość i wysokość muszą być wartościami większymi od 0.'
            );
        }
        $this->_destroyImage();
        $this->_resource = imagecreatetruecolor($width, $height);
        if (!$this->_resource) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Błąd podczas tworzenia zasobu obrazu.');
        }
        $this->_setSize($width, $height);
        return $this;
    }

    /**
     * Otwarcie pliku graficznego i zwrócenie uchwytu
     *
     * @param string $path                  Ścieżka do pliku
     * @return Modern_Image_Adapter      Zwraca referencję do adaptera ($this)
     */
    public function open($path)
    {
        if (!file_exists($path)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Bledna nazwa pliku lub sciezki.');
        }
        if (!$this->_isSupportedType($path)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Typ pliku nie jest obslugiwany.');
        }

        switch ($this->_getType($path)) {
            case Modern_Image_Adapter::JPG:
                $this->imageType = Modern_Image_Adapter::JPG;
                $this->_resource = imagecreatefromjpeg($path);
                break;
            case Modern_Image_Adapter::GIF:
                $this->imageType = Modern_Image_Adapter::GIF;
                $this->_resource = imagecreatefromgif($path);
                break;
            case Modern_Image_Adapter::PNG:

                $this->imageType = Modern_Image_Adapter::PNG;
                $this->_resource = imagecreatefrompng($path);
                break;
        }
        $this->_setSize();

        return $this;
    }

    /**
     * Zapisanie obrazu do pliku
     *
     * @param string $destFile Nazwa i ścieżka dla nowego pliku
     * @return boolean
     */
    public function save($destFile)
    {
        $ext = pathinfo($destFile, PATHINFO_EXTENSION);
        $destFile = preg_replace("|.$ext$|", '', trim($destFile));

        switch ($this->_config->imageType) {
            case Modern_Image_Adapter::JPG:
                $result = imagejpeg($this->_resource, "$destFile.jpg" , $this->_config->quality);
                break;
            case Modern_Image_Adapter::PNG:
                $this->_saveAlphaData($this->_resource);
                $result = imagepng(
                    $this->_resource, "$destFile.png",
                    $this->_qualityToPngCompress($this->_config->quality)
                );
                break;
            case Modern_Image_Adapter::GIF:
                $result = imagegif($this->_resource, "$destFile.gif");
                break;
        }
        return (bool) $result;
    }

    /**
     * Pobranie wymiarów obrazu
     *
     * @param string|null    Identyfikator wymiaru (predefiniowana stała klasowa)
     * @return array|integer Tablica zawierająca szerokość i wysokość obrazu
     */
    public function getSize($dimension=null)
    {
        switch ($dimension) {
            case Modern_Image_Adapter::WIDTH:
                return $this->_size[Modern_Image_Adapter::WIDTH];
            case Modern_Image_Adapter::HEIGHT:
                return $this->_size[Modern_Image_Adapter::HEIGHT];
            default:
                return $this->_size;
        }
    }

    /**
     * Dodanie znaku wodnego do obrazu
     *
     * @param string $file                Ścieżka do|nazwa pliku znaku wodnego
     * @param string|null $position       Pozycja obiektu (predefiniowane stałe klasowe)
     * @param integer|null $waterPaddingX Odstęp znaku wodnego od pionowej krawędzi obrazu (przesunięcie w osi X)
     * @param integer|null $waterPaddingY Odstęp znaku wodnego od poziomej krawędzi obrazu (przesunięcie w osi Y)
     * @return Modern_Image_Adapter       Zwraca referencję do adaptera ($this)
     */
    public function addWatermark($file, $position=null, $paddingX=null, $paddingY=null)
    {
        if (!file_exists($file)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Bledna nazwa lub sciezka do pliku znaku wodnego.');
        }
        if ($paddingX < 0 || $paddingY < 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Wartość paddingu musi być większa od 0.');
        }
        if (null === $position) {
            $position = $this->_config->waterPosition;
        }

        $water = new self();

        $waterSize = $water->open($file)->getSize();

        // Obliczenia wspolrzednych znaku wodnego dla okreslonej pozycji
        $start = $this->_calcPos($waterSize, $position, $paddingX, $paddingY);

        $controll = imagecopymerge(
            $this->_resource, $water->getResource(),
            $start[Modern_Image_Adapter::XPOS],
            $start[Modern_Image_Adapter::YPOS],
            0, 0,
            $waterSize[Modern_Image_Adapter::WIDTH],
            $waterSize[Modern_Image_Adapter::HEIGHT],
            $this->_config->waterDensity
        );
        if (!$controll) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Błąd podczas przetwarzania zasobu obrazu.');
        }

        return $this;
    }

    /**
     * Obrót obrazu o dowolny kąt
     *
     * @param float $angle          Kąt obrotu
     * @param hex $bgColor          Kolor tła po obrocie (jeśli null to tło jest transparentne)
     * @return Modern_Image_Adapter Zwraca referencje do adaptera ($this)
     */
    public function rotate($angle, $bgColor=null)
    {
        if (!function_exists('imagerotate')) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Funkcja imagerotate nie jest zainstalowana');
        }

        if (empty($this->_resource)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Brak zasobu obrazu');
        }

        // Obrot z kolorem tla
        if (null != $bgColor) {
            $bgColor = (int) (hexdec($bgColor));
            if ($this->_config->imageType != 'gif') {
                $this->_resource = imagerotate($this->_resource, (float) $angle, $bgColor);
            } else {
                $color = imagecolorallocate($this->_resource, 0, 0, 0);
                $colTrans = imagecolortransparent($color);
                $this->_resource = imagerotate($this->_resource, (float) $angle, $colTrans);
            }
        }
        // Obrot z transparentnym tlem
        else {
            $colTrans = imagecolortransparent($this->_resource);
            if ($this->_config->imageType != 'jpg') {
                $color = imagecolorallocate($this->_resource, 0, 0, 0);
                $colTrans = imagecolortransparent($color);
            }
            $this->_resource = imagerotate($this->_resource, (float) $angle, $colTrans);
        }
        return $this;
    }

    /**
     * Ustawia zasób obrazu
     *
     * @param resource $resource
     * @return Modern_Image_Adapter
     */
    public function setResource($resource)
    {
        if (!is_resource($resource) || 'gd' != get_resource_type($resource)) {
            throw new Exception('$resource musi być zasobem GD');
        }

        $this->_resource = $resource;
        $this->_setSize();
        return $this;
    }

}