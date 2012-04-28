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
 * @subpackage  Manager
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
/** @see Modern_Image */
require_once 'Modern/Image.php';

/**
 * Klasa realizująca generację miniatur dla plików graficznych.
 *
 * @category    Modern
 * @package     Modern_Image
 * @subpackage  Manager
 * @author      Rafał Gałka <rafal.galka@modernweb.pl>
 * @copyright   Copyright (c) 2007-2010 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Image_Manager
{
    /**
     * Ścieżka absolutna do wygenerowanych plików
     *
     * @var string
     */
    protected $_destinationPath = null;

    /**
     * Baza adresowa do plików wygenerowanych
     *
     * @var string
     */
    protected $_destinationUrl = null;

    /**
     * Flaga mówiąca czy manager ma tworzyć foldery dla konkretnych typów z miniaturkami
     *
     * @var boolean
     */
    protected $_useNamesAsFolders = false;

    /**
     * Głębokość folderów przechowujących miniaturki
     *
     * @var integer
     */
    protected $_folderDepth = 0;

    /**
     * Szablony konfiguracji zdjęć
     *
     * @var array
     */
    protected $_types = null;

    /**
     * Adapter do Imaga
     *
     * @var Modern_Image_Adapter
     */
    protected $_image = null;

    /**
     * Szablon konfiguracji typów konwersji zdjęć
     *
     * @var array
     */
    protected $_defaults = Array(
        'resize' => array(
            'width' => 0,
            'height' => 0,
            'keepAspectRatio' => null,
            'forceResize' => null
        ),
        'resizeTo' => array(
            'newSize' => 0,
            'baseDim' => 'width',
            'keepAspectRatio' => null,
            'forceResize' => null
        ),
        'thumbnail' => array(
            'width' => 0,
            'height' => 0
        ),
        'original' => array(
            'width' => 0,
            'height' => 0,
            'keepAspectRatio' => null,
            'forceResize' => null
        ),
    );

    /**
     * Konstruktor - przekazanie konfiguracji
     *
     * @param Zend_Config|array $configuration
     */
    public function __construct($configuration)
    {
        if (is_array($configuration)) {
            $configuration = new Zend_Config($configuration);
        }
        // Pobranie bazowego url'a (na wszelki wypadek jak by było odpalane w podkatalogu)
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        /**
         * Konfiguracja ścieżki docelowej (parametr <destinationUrl>)
         * ścieżka wskazuje na katalog w którym przechowywane będą wygenerowane miniaturki
         */
        $this->_destinationUrl = $configuration->get('destinationUrl', null);

        if (null === $this->_destinationUrl) {
            throw new Modern_Image_Manager_Exception('Ścieżka docelowa nie została skonfigurowana');
        }

        // Przygotowanie ścieżki absolutnej do katalogu
        $this->_destinationPath = rtrim($configuration->get('destinationUrl'), '/');

        if (!is_dir($this->_destinationPath)) {
            umask(0);
            mkdir($this->_destinationPath, 0777, true);
        }

        /**
         * Parametr <useNamesAsFolders> mówi o tym czy Manager ma tworzyć podkatalogi w katalogu bazowym
         * o nazwie zgodnej z nazwą szablonu rozmiarowego. Daje to możliwość składowania miniaturek w osobnych katalogach.
         */
        $this->_useNamesAsFolders = (boolean) $configuration->get('useNamesAsFolders', $this->_useNamesAsFolders);

        /**
         * Parametr <folderDepth> wskazuje ile podkatalogów ma być tworzonych na podstawie nazwy pliku. Algorytm
         * nazw polegać będzie na pobieraniu po literce z nazwy pliku i tworzeniu katalogu o tej nazwie.
         *
         * plik abcdefghjk.jpg przy parametrze <folderDepth>=3 zapisany zostanie w podkatalogach:
         * /<ścieżka bazowa>/<ew. nazwa typu>/a/b/c/abcdefghjk.jpg
         */
        $this->_folderDepth = (int) $configuration->get('folderDepth', $this->_folderDepth);

        /**
         * Maksymalna głębokość to 32 bo taka jest długość nazwy pliku tymczasowego.
         */
        if ($this->_folderDepth > 32) {
            throw new Modern_Image_Manager_Exception(
                "Maksymalna głębokość katalogów 32, podano: '{$this->_folderDepth}'");
        }

        /**
         * Szablony rozmiarów: tablica z informacją w jaki sposób ma być skalowana miniaturka
         *
         * <nazwa typu>
         *      <default>           - ścieżka do pliku domyślnego
         *      <type>              - algorytm resizowania
         *          - resize: skalowanie do box'a
         *          - resizeTo: skalowanie do wybranej krawędzi
         *          - thumbnail: skalowanie z cropowaniem
         *      <baseDim>           - do której z krawędzi ma resizować (dla typu resizeTo)
         *      <newSize>           - jaką wielkość ma mieć krawędź opisana w parametrze baseDim
         *          - width
         *          - height
         *      <width>             - szerokość docelowa
         *      <height>            - wysokość docelowa
         *      <keepAspectRatio>   - czy mają być zachowane proporcje
         *      <forceResize>       - czy jeśli obraz docelowy jest mniejszy ma go powiększać
         */
        $this->_types = $configuration->get('type', null);
        if (null === $this->_types) {
            $this->_types = array();
        } else {
            $this->_types = $this->_types->toArray();
        }

        /**
         * Powołanie obiektu Image
         */
        $this->_image = Modern_Image::factory('gd2');
    }

    /**
     * Konwersja pliku
     *
     * @param string $fileName
     * @param string $type
     */
    public function get($fileName, $type)
    {
        if (!isset($this->_types[$type])) {
            throw new Modern_Image_Manager_Exception("Brak zdefiniwanego typu '$type'");
        }

        /**
         * Sprawdza czy istnieje plik źródłowy
         */
        var_dump($fileName);
        exit;
        $file = ROOT_PATH . $fileName;
        if (!file_exists($file) || is_dir($file)) {
            if (isset($this->_types[$type]['default'])) {
                $fileName = $this->_types[$type]['default'];
                $file = ROOT_PATH . $this->_types[$type]['default'];
            } else {
                /**
                 * Zwraca pusty ciąg w momencie gdy nie ma pliku źródłowego i domyślnego
                 */
                return '';
            }
        }

        /**
         * Budowanie ścieżki do pliku miniaturki
         */
        $tempFileName = $this->_generateTempFileName($fileName, $type);

        // Jeśli plik istnieje, zwraca adres
        if (file_exists($this->_destinationPath . $tempFileName)) {
            return $this->_destinationUrl . $tempFileName;
        }

        $this->_generateThumbFile($file, $this->_destinationPath . $tempFileName, $type);
        return $this->_destinationUrl . $tempFileName;
        // Jeśli nie generuje nowy
    }

    /**
     * Generuje nazwę pliku tymczasowego wraz ze ścieżką do głównego katalogu thumbnaili na podstawie
     * konfiguracji.
     *
     * @param string $fileName
     * @param string $type
     * @return string
     */
    protected function _generateTempFileName($fileName, $type)
    {
        $path = '';

        // Dokleja nazwę typu do ściezki
        if ($this->_useNamesAsFolders) {
            $path .= '/' . $type;
        }

        /**
         * md5 daje 32 ciąg znaków który da się jeszcze po równo porozrzucać po katalogach
         */
        $thumbName = md5($type . $fileName) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

        // Budowanie ścieżki składającej się z liter
        $i = 0;
        while ($i < $this->_folderDepth) {
            $path .= '/' . $thumbName{$i};
            $i++;
        }

        // Gotowa ścieżka
        $path .= '/' . $thumbName;

        return $path;
    }

    /**
     * Generuje plik z miniaturkami
     *
     * @param string $source
     * @param string $destination
     * @param string $type
     */
    protected function _generateThumbFile($source, $destination, $type)
    {
        $method = $this->_types[$type]['type'];

        // Przygotowanie gotowego zestawu parametrów dla każdej z metod
        $params = array_values(
            array_merge($this->_defaults[$method], array_intersect_key($this->_types[$type], $this->_defaults[$method]))
        );

        if (isset($this->_types[$type]['quality']) && (int) $this->_types[$type]['quality'] > 0) {
            $this->_image->setQuality((int) $this->_types[$type]['quality']);
        }

        if (isset($this->_types[$type]['forceResize'])) {
            $this->_image->setForceResize((boolean) $this->_types[$type]['forceResize']);
        }

        // Wykonanie miniatury
        $base = dirname($destination);
        if (!file_exists($base)) {
            mkdir($base, 0777, true);
        }

        $this->_image->open($source);
        if ('original' !== $method) {
            call_user_func_array(array($this->_image, $method), $params);
        }
        $this->_image->save($destination);
    }

}