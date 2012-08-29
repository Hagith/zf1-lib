<?php

/**
 * Modern
 *
 * LICENSE
 *
 * This source file is subject to version 1.0
 * of the ModernWeb license.
 *
 * @category    Modern
 * @package     Modern_Image
 * @subpackage  Adapter
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */

/** @see Zend_Config */
require_once('Zend/Config.php');

/**
 * Klasa abstrakcyjna dla adapterów komponentu Modern_Image
 *
 * @category    Modern
 * @package     Modern_Image
 * @subpackage  Adapter
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
abstract class Modern_Image_Adapter
{
    /**
     * Stałe nazw szerokości i wysokości
     *
     * @var string
     */
    const WIDTH = 'width';
    const HEIGHT = 'height';

    /**
     * Stałe określające nazwy współrzędnych położenia
     *
     * @var string
     */
    const XPOS = 'x';
    const YPOS = 'y';

    /**
     * Stałe określające nazwy położenia
     *
     * @var string
     */
    const TOPLEFT = 'topleft';
    const TOPRIGHT = 'topright';
    const BOTTOMLEFT = 'bottomleft';
    const BOTTOMRIGHT = 'bottomright';
    const CENTER = 'center';

    /**
     * Stale definiujace nazwy typów plikow graficznych
     *
     * @var string
     */
    const JPG = 'jpg';
    const GIF = 'gif';
    const PNG = 'png';

    /**
     * Uchwyt pliku graficznego
     *
     * @var resource
     */
    protected $_resource = null;

    /**
     * Tablica konfiguracyjna
     *
     * @var array|Zend_Config
     */
    protected $_config = array(
        'imageType' => self::JPG,
        'quality' => 75,
        'waterDensity' => 50,
        'waterPaddingX' => 0,
        'waterPaddingY' => 0,
        'waterPosition' => self::BOTTOMRIGHT,
        'keepAspectRatio' => true,
        'forceResize' => false,
    );

    /**
     * Wymiary obrazu
     *
     * @var array
     */
    protected $_size = array(
        self::WIDTH => 0,
        self::HEIGHT => 0
    );

    /**
     * Konstruktor
     *
     * @param array|Zend_Config $config
     */
    public function __construct($config = null)
    {
        $this->_config = new Zend_Config($this->_config, true);
        if (null !== $config) {
            $this->_setInitParams($config);
        }
    }

    /**
     *  Ustawienie wartości parametrów konfiguracyjnych
     *
     *  @param string $property         Nazwa parametru konfiguracyjnego
     *  @param mixed $value             Wartość parametru konfiguracyjnego
     *  @return void
     */
    public function __set($property, $value)
    {
        switch ($property) {
            case 'imageType':
                $value = strtolower(trim($value));
                switch ($value) {
                    case 'jpg':
                    case 'jpeg':
                    case 'pjpeg':
                        $this->_config->imageType = self::JPG;
                        break;
                    case 'png':
                        $this->_config->imageType = self::PNG;
                        break;
                    case 'gif':
                        $this->_config->imageType = self::GIF;
                        break;
                    default:
                        /** @see Modern_Image_Adapter_Exception */
                        require_once('Modern/Image/Adapter/Exception.php');
                        throw new Modern_Image_Adapter_Exception('Typ pliku nie jest obslugiwany.');
                        break;
                }
                break;

            case 'quality':
            case 'waterDensity':
                $value = (int) $value;
                if ($value < 0 || $value > 100) {
                    /** @see Modern_Image_Adapter_Exception */
                    require_once('Modern/Image/Adapter/Exception.php');
                    throw new Modern_Image_Adapter_Exception("Wartosc dyrektywy '$property' spoza zakresu 0-100.");
                }
                $this->_config->{$property} = $value;
                break;

            case 'waterPaddingX':
            case 'waterPaddingY':
                $this->_config->{$property} = (int) $value;
                break;

            case 'keepAspectRatio':
            case 'forceResize':
                $this->_config->{$property} = (bool) $value;
                break;

            default:
                /** @see Modern_Image_Adapter_Exception */
                require_once('Modern/Image/Adapter/Exception.php');
                throw new Modern_Image_Adapter_Exception("Brak podanej dyrektywy konfiguracyjnej '$property'.");
        }
    }

    /**
     *  Pobranie wartości parametrów konfiguracyjnych
     *
     *  @param string $property         Nazwa parametru konfiguracyjnego
     *  @return mixed
     */
    public function __get($property)
    {
        if (isset($this->_config->{$property})) {
            return $this->_config->{$property};
        }
        /** @see Modern_Image_Adapter_Exception */
        require_once('Modern/Image/Adapter/Exception.php');
        throw new Modern_Image_Adapter_Exception("Brak podanej dyrektywy konfiguracyjnej '$property'.");
    }

    /**
     * Metoda definiuje settery dla dyrektyw konfiguracyjnych.
     * Przykład: $img->setKeepAspectRatio(true)->setForceResize(false)->setQuality(80);
     *
     * @param string $name
     * @param array $values
     * @return Modern_Image_Adapter
     */
    public function __call($name, $values)
    {
        if (strpos($name, 'set') === 0) {
            $property = str_replace('set', '', $name);
            $property{0} = strtolower($property{0});
            $this->__set($property, $values[0]);
            return $this;
        }
        /** @see Modern_Image_Adapter_Exception */
        require_once('Modern/Image/Adapter/Exception.php');
        throw new Modern_Image_Adapter_Exception("Nieobsługiwane wywołanie metody '$name' przechwycone w __call()");
    }

    /**
     * Zwraca zasób obrazu
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Zmiana rozmiarów obrazu
     *
     * @param integer $width                    Szerokość obrazu
     * @param integer $height                   Wysokość obrazu
     * @param boolean|null $keepAspectRatio     Parametr określający, czy ma zostać zachowany stosunek długości do szerokości
     * @param boolean|null $forceResize         Parametr okreslający, czy dozwolone jest zwiększenie podczas operacji resize oryginalnych wymiarów
     * @return Modern_Image_Adapter
     */
    public function resize($width, $height, $keepAspectRatio=null, $forceResize=null)
    {
        if (empty($this->_resource)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Brak zasobu obrazu.');
        }

        // Sprawdzenie parametrow keepAspectRatio i forceResize
        // Jesli nie zostaly podane, to zaladowanie z configu
        if (null === $keepAspectRatio) {
            $keepAspectRatio = $this->_config->keepAspectRatio;
        }
        if (null === $forceResize) {
            $forceResize = $this->_config->forceResize;
        }

        // Wymiary boxu
        $width = (int) $width;
        $height = (int) $height;

        if ($width <= 0 || $height <= 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Wartosci wymiarow obrazu musza byc dodatnie.');
        }

        // Obraz miesci sie wewnatrz boxu, ale nie moze byc powiekszony
        if (
            !$forceResize &&
            $width >= $this->_size[self::WIDTH] &&
            $height >= $this->_size[self::HEIGHT]
        ) {
            return $this;
        }

        // Rzadki przypadek, ale nigdy nie wiadomo ;)
        if ($width == $this->_size[self::WIDTH] && $height == $this->_size[self::HEIGHT]) {
            return $this;
        }

        $xRat = ($width / $this->_size[self::WIDTH]);
        $yRat = ($height / $this->_size[self::HEIGHT]);

        // Zachowanie stosunku wymiarow
        if ($keepAspectRatio) {
            if ($xRat <= $yRat) {
                if ($xRat > 1 && !$forceResize) {
                    return $this;
                }
                $height = (int) ($this->_size[self::HEIGHT] * $xRat);
            } else {
                if ($yRat > 1 && !$forceResize) {
                    return $this;
                }
                $width = (int) ($this->_size[self::WIDTH] * $yRat);
            }
        }
        // Bez zachowania stosunku wymiarow
        else {
            if (!$forceResize) {
                if ($xRat > 1) {
                    $width = $this->_size[self::WIDTH];
                }
                if ($yRat > 1) {
                    $height = $this->_size[self::HEIGHT];
                }
            }
        }

        $this->_resize($width, $height);
        $this->_setSize($width, $height);

        return $this;
    }

    /**
     * Zmiana rozmiarów obrazu do bazowego wymiaru
     *
     * @param integer $newSize                      Wartość bazowego wymiaru
     * @param string $baseDim                       Identyfikator/nazwa bazowego wymiaru
     * @param boolean|null $keepAspectRatio         Kontroler zachowania stosunku wymiarów (domyślnie wartość konfiguracyjna)
     * @param boolean|null $forceResize             Kontroler możliwości zwiększenia oryginalnych wymiarów (domyślnie wartość konfiguracyjna)
     * @return Modern_Image_Adapter
     */
    public function resizeTo($newSize, $baseDim, $keepAspectRatio=null, $forceResize=null)
    {
        if (empty($this->_resource)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Brak zasobu obrazu.');
        }

        $newSize = (int) $newSize;

        if ($newSize <= 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Wartość wymiaru obrazu musi być dodatnia.');
        }

        // Sprawdzenie i ustawienie parametrow keepAspectRatio i forceResize
        if (null === $keepAspectRatio) {
            $keepAspectRatio = $this->_config->keepAspectRatio;
        }
        if (null === $forceResize) {
            $forceResize = $this->_config->forceResize;
        }

        if ($baseDim !== self::WIDTH && $baseDim !== self::HEIGHT) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Nie podano prawidłowego identyfikatora wymiaru bazowego.');
        }
        $size = array(
            self::WIDTH => $this->_size[self::WIDTH],
            self::HEIGHT => $this->_size[self::HEIGHT]
        );

        $ratio = array(
            self::WIDTH => $this->_size[self::WIDTH] / $this->_size[self::HEIGHT],
            self::HEIGHT => $this->_size[self::HEIGHT] / $this->_size[self::WIDTH]
        );

        $size[$baseDim] = $newSize;

        if (!$forceResize && $size[$baseDim] > $this->_size[$baseDim]) {
            return $this;
        }

        // Zachowanie stosunku wymiarow
        if ($keepAspectRatio) {
            $missingDim = ($baseDim == self::WIDTH) ? self::HEIGHT : self::WIDTH;
            $size[$missingDim] = (int) ($size[$baseDim] * $ratio[$missingDim]);
        }

        $this->_resize($size[self::WIDTH], $size[self::HEIGHT]);
        $this->_setSize($size[self::WIDTH], $size[self::HEIGHT]);

        return $this;
    }

    /**
     * Utworzenie miniatury obrazu
     *
     * @param integer $width                Szerokość miniatury
     * @param integer $height               Wysokość miniatury
     * @return Modern_Image_Adapter
     */
    public function thumbnail($width, $height)
    {
        if (empty($this->_resource)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Brak zasobu obrazu.');
        }

        $width = (int) $width;
        $height = (int) $height;

        if ($width <= 0 || $height <= 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Szerokosc i wysokosc miniatury musza byc dodatnie.');
        }

        $widthRatio = $width / $this->_size[self::WIDTH];
        $heightRatio = $height / $this->_size[self::HEIGHT];

        if ($widthRatio > $heightRatio) {
            $this->resizeTo($width, self::WIDTH, true, true);
            $startY = ($this->_size[self::HEIGHT] - $height) / 2;
            $this->crop(0, $startY, $width, $height);
        } else {
            $this->resizeTo($height, self::HEIGHT, true, true);
            $startX = ($this->_size[self::WIDTH] - $width) / 2;
            $this->crop($startX, 0, $width, $height);
        }

        $this->_setSize($width, $height);
        return $this;
    }

    /**
     * Wycięcie fragmentu obrazu
     *
     * @param integer $startX               Współrzędna X lewego górnego rogu wycinanego elementu
     * @param integer $startY               Współrzędna Y lewego górnego rogu wycinanego elementu
     * @param integer $width                Szerokość wycinanego elementu
     * @param integer $height               Wysokość wycinanego elementu
     * @return Modern_Image_Adapter
     */
    public function crop($startX, $startY, $width, $height)
    {
        if (empty($this->_resource)) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Brak zasobu obrazu.');
        }

        $startX = (int) $startX;
        $startY = (int) $startY;
        $width = (int) $width;
        $height = (int) $height;

        if ($startX < 0 || $startY < 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Punkty startowe nie mogą być wartościami ujemnymi.');
        }
        // Sprawdzenie, czy poczatkowe punkty X,Y leza w obrebie obrazu
        if ($startX > $this->_size[Modern_Image_Adapter::WIDTH] && $startY > $this->_size[Modern_Image_Adapter::HEIGHT]) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Punkty startowe muszą być zawarte w obszarze obrazu.');
        }
        if ($width <= 0 || $height <= 0) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Szerokosc i wysokosc nie mogą być wartościami ujemnymi.');
        }

        // Sprawdzenie, czy wycinany fragment miesci sie w oryginalnym obrazie
        $ctrlX = $startX + $width;
        $ctrlY = $startY + $height;

        if ($ctrlX > $this->_size[Modern_Image_Adapter::WIDTH]) {
            $width = $this->_size[Modern_Image_Adapter::WIDTH] - $startX;
        }
        if ($ctrlY > $this->_size[Modern_Image_Adapter::HEIGHT]) {
            $height = $this->_size[Modern_Image_Adapter::HEIGHT] - $startY;
        }

        $this->_crop($startX, $startY, $width, $height);
        $this->_setSize();

        return $this;
    }

    /**
     * Wczytanie parametrów z tablicy konfiguracyjnej
     *
     * @param array|Zend_Config $config
     */
    private function _setInitParams($config)
    {
        if (!is_array($config) && !$config instanceof Zend_Config) {
            /** @see Modern_Image_Adapter_Exception */
            require_once('Modern/Image/Adapter/Exception.php');
            throw new Modern_Image_Adapter_Exception('Konfiguracja musi być tablicą lub obiektem Zend_Config.');
        }
        if (is_array($config)) {
            $config = new Zend_Config($config);
        }

        foreach ($config as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Pobranie typu pliku
     *
     * @param string $file          Nazwa (+ścieżka) pliku
     * @return string               Identyfikator typu pliku (np.: 'jpg')
     */
    protected function _getType($file)
    {
        switch (exif_imagetype($file)) {
            case IMAGETYPE_GIF:
                return self::GIF;
            case IMAGETYPE_JPEG:
                return self::JPG;
            case IMAGETYPE_PNG:
                return self::PNG;
            default:
                /** @see Modern_Image_Adapter_Exception */
                require_once('Modern/Image/Adapter/Exception.php');
                throw new Modern_Image_Adapter_Exception('Typ pliku nie jest obsługiwany.');
        }
    }

    /**
     *  Sprawdzenie, czy dany typ pliku jest obsługiwany
     *
     *  @param string $file             Ścieżka|Nazwa pliku
     *  @return boolean
     */
    protected function _isSupportedType($file)
    {
        if (false !== (array_search($this->_getType($file), $this->_getSupportedTypes()))) {
            return true;
        }
        return false;
    }

    /**
     *  Metoda obliczająca pozycję obiektu (lewy górny róg) na większym obiekcie/obrazie
     *
     *  @param array $mark                      Tablica wymiarów pozycjonowanego obiektu
     *  @param string $position                 Pozycja obiektu (predefiniowane stałe klasowe)
     *  @param integer|null $paddingX           Odstęp od pionowej krawędzi obrazu (przesunięcie w osi X)
     *  @param integer|null $paddingY           Odstęp od poziomej krawędzi obrazu (przesunięcie w osi Y)
     *  @return array                           Tablica współrzędnych (x,y) lewego górnego rogu znaku wodnego
     */
    protected function _calcPos(array $mark, $position, $paddingX=null, $paddingY=null)
    {
        $startPos = array(
            self::XPOS => 0,
            self::YPOS => 0
        );

        // Ustalenie aktualnego paddingu
        if (null === $paddingX) {
            $paddingX = $this->_config->waterPaddingX;
        } else {
            $paddingX = (int) $paddingX;
        }
        if (null === $paddingY) {
            $paddingY = $this->_config->waterPaddingY;
        } else {
            $paddingY = (int) $paddingY;
        }

        // Obliczenia wspolrzednych znaku dla okreslonej pozycji
        switch ($position) {

            // Pozycja TOPLEFT
            case self::TOPLEFT:
                $startPos[self::XPOS] = 0 + $paddingX;
                $startPos[self::YPOS] = 0 + $paddingY;
                break;

            // Pozycja TOPRIGHT
            case self::TOPRIGHT:
                $startPos[self::XPOS] = $this->_size[self::WIDTH] - $mark[self::WIDTH] - $paddingX;
                $startPos[self::YPOS] = $paddingY;
                break;

            // Pozycja BOTTOMLEFT
            case self::BOTTOMLEFT:
                $startPos[self::XPOS] = $paddingX;
                $startPos[self::YPOS] = $this->_size[self::HEIGHT] - $mark[self::HEIGHT] - $paddingY;
                break;

            // Pozycja BOTTOMRIGHT
            case self::BOTTOMRIGHT:
                $startPos[self::XPOS] = $this->_size[self::WIDTH] - $mark[self::WIDTH] - $paddingX;
                $startPos[self::YPOS] = $this->_size[self::HEIGHT] - $mark[self::HEIGHT] - $paddingY;
                break;

            // Pozycja CENTER
            case self::CENTER:

                $tmpx = $this->_size[self::WIDTH] - $mark[self::WIDTH];
                $tmpy = $this->_size[self::HEIGHT] - $mark[self::HEIGHT];

                // Szerokosc znaku wodnego wieksza niz obrazu
                if ($tmpx <= 0) {
                    $startPos[self::XPOS] = 0;
                }
                // Szerokosc znaku wodnego mniejsza niz obrazu
                else {
                    $startPos[self::XPOS] = round(($tmpx / 2));
                }

                // Wysokosc znaku wodnego wieksza niz obrazu
                if ($tmpy <= 0) {
                    $startPos[self::YPOS] = 0;
                }
                // Wysokosc znaku wodnego mniejsza niz obrazu
                else {
                    $startPos[self::YPOS] = round(($tmpy / 2));
                }

                break;
        }

        return $startPos;
    }

    /**
     * Utworzenie nowego zasobu obrazu
     *
     * @param integer $width                Szerokość obrazu
     * @param integer $height               Wysokość obrazu
     * @return Modern_Image_Adapter
     */
    abstract public function create($width, $height);
    /**
     * Otwarcie pliku graficznego i zwrócenie uchwytu
     *
     * @param string $path                  Ścieżka do pliku
     * @return Modern_Image_Adapter      Zwraca referencje do adaptera ($this)
     */
    abstract public function open($path);
    /**
     * Zapisanie obrazu do pliku
     *
     * @param string $destFile              Nazwa i ścieżka dla nowego pliku
     * @return boolean
     */
    abstract public function save($destFile);
    /**
     * Pobranie wymiarów obrazu
     *
     * @param string|null $dimension       Identyfikator wymiaru (predefiniowana stała klasowa)
     * @return array|integer               Tablica zawierająca szerokość i wysokość obrazu
     */
    abstract public function getSize($dimension=null);
    /**
     *  Dodanie znaku wodnego do obrazu
     *
     *  @param string $file                     Ścieżka do|nazwa pliku znaku wodnego
     *  @param string|null $position            Pozycja obiektu (predefiniowane stałe klasowe)
     *  @param integer|null $paddingX           Odstęp znaku wodnego od pionowej krawędzi obrazu (przesunięcie w osi X)
     *  @param integer|null $paddingY           Odstęp znaku wodnego od poziomej krawędzi obrazu (przesunięcie w osi Y)
     *  @return Modern_Image_Adapter
     */
    abstract public function addWatermark($file, $position=null, $paddingX=null, $paddingY=null);
    /**
     * Obrót obrazu o dowolny kąt
     *
     * @param float $angle                       Kąt obrotu
     * @param integer|null $bgColor              Kolor tła po obrocie (jeśli null to tło jest transparentne)
     * @return Modern_Image_Adapter
     */
    abstract public function rotate($angle, $bgColor=null);
    /**
     *  Metoda realizująca przetworzenie zasobu obrazu do określonych wymiarów
     *
     * @param integer $startX               Współrzędna X lewego górnego rogu wycinanego elementu
     * @param integer $startY               Współrzędna Y lewego górnego rogu wycinanego elementu
     * @param integer $width                Szerokość wycinanego elementu
     * @param integer $height               Wysokość wycinanego elementu
     * @return void
     */
    abstract protected function _crop($startX, $startY, $width, $height);
    /**
     *  Metoda realizująca przekształcenie zasobu obrazu do określonych wymiarów
     *
     *  @param integer $width           Szerokość uzyskana z obliczeń w metodach resize() i resizeTo()
     *  @param integer $height          Wysokość uzyskana z obliczeń w metodach resize() i resizeTo()
     *  @return void
     */
    abstract protected function _resize($width, $height);
    /**
     *  Zniszczenie zasobu obrazu, jeśli istnieje
     *
     *  @return void
     */
    abstract protected function _destroyImage();
    /**
     * Pobranie obsługiwanych typów plików graficznych
     *
     *  @return array
     */
    abstract protected function _getSupportedTypes();
    /**
     *  Ustawienie wewnętrznej tablicy wymiarów obrazu na wartości aktualne
     *
     *  @param integer|null         Szerokość
     *  @param integer|null         Wysokość
     *  @return void
     */
    abstract protected function _setSize($width=null, $height=null);
}