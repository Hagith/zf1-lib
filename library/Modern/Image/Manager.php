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
 * @subpackage  Manager
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */
/** @see Modern_Image */
require_once 'Modern/Image.php';

/**
 * Image thumbnail manager.
 *
 * @category    Modern
 * @package     Modern_Image
 * @subpackage  Manager
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Modern_Image_Manager
{
    /**
     * Document root path.
     *
     * @var string
     */
    protected $_documentRoot;

    /**
     * Thumbnails directory (relative to $this->_documentRoot).
     *
     * @var string
     */
    protected $_thumbsDir;

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
     * Image adapter
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
     * @param Zend_Config|array $config
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            $config = new Zend_Config($config);
        }

        $this->setDocumentRoot($config->documentRoot);
        $this->setThumbsDir($config->thumbsDir);

        if (!is_dir($this->getThumbsDir(true))) {
            mkdir($this->getThumbsDir(true), 0777, true);
        }

        /**
         * Parametr <useNamesAsFolders> mówi o tym czy Manager ma tworzyć podkatalogi w katalogu bazowym
         * o nazwie zgodnej z nazwą szablonu rozmiarowego. Daje to możliwość składowania miniaturek w osobnych katalogach.
         */
        $this->_useNamesAsFolders = (boolean) $config->get('useNamesAsFolders', $this->_useNamesAsFolders);

        /**
         * Parametr <folderDepth> wskazuje ile podkatalogów ma być tworzonych na podstawie nazwy pliku. Algorytm
         * nazw polegać będzie na pobieraniu po literce z nazwy pliku i tworzeniu katalogu o tej nazwie.
         *
         * plik abcdefghjk.jpg przy parametrze <folderDepth>=3 zapisany zostanie w podkatalogach:
         * /<ścieżka bazowa>/<ew. nazwa typu>/a/b/c/abcdefghjk.jpg
         */
        $this->_folderDepth = (int) $config->get('folderDepth', $this->_folderDepth);

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
        $this->_types = $config->get('type', null);
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
     * @param string $filename
     * @param string $type
     */
    public function get($filename, $type)
    {
        if (!isset($this->_types[$type])) {
            throw new Modern_Image_Manager_Exception("Brak zdefiniwanego typu '$type'");
        }

        $filename = ltrim($filename, '/');
        $path = $this->getDocumentRoot() . $filename;

        // check if source file exists with fallback to default image
        if (!is_file($path) && isset($this->_types[$type]['default'])) {
            $filename = ltrim($this->_types[$type]['default'], '/');
            $path = $this->getDocumentRoot() . $filename;
        }

        if (!is_file($path)) {
            // throw exception when wrong configuration provided
            if (isset($this->_types[$type]['default'])) {
                throw new Modern_Image_Manager_Exception(
                    "Default image '$path' for type '$type' does not exists"
                );
            }

            // return empty string when no source file
            return '';
        }

        // prepare thumbnail path
        $thumb = $this->getThumbsDir(true) . $this->_generateTempFileName($filename, $type);

        // if thumbnail already exist do nothing
        if (is_file($thumb)) {
            return $thumb;
        }

        // generate thumbnail
        $this->_generateThumbFile($path, $thumb, $type);

        return $thumb;
    }

    public function getDocumentRoot()
    {
        if (!$this->_documentRoot && isset($_SERVER['DOCUMENT_ROOT'])) {
            $this->setDocumentRoot($_SERVER['DOCUMENT_ROOT']);
        }

        return $this->_documentRoot;
    }

    public function setDocumentRoot($path)
    {
        $this->_documentRoot = rtrim($path, '/') . '/';

        return $this;
    }

    public function getThumbsDir($absolute = false)
    {
        if ($absolute) {
            return $this->getDocumentRoot() . $this->_thumbsDir;
        }

        return $this->_thumbsDir;
    }

    public function setThumbsDir($thumbsDir)
    {
        $this->_thumbsDir = rtrim($thumbsDir, '/');

        return $this;
    }

    /**
     * Generuje nazwę pliku tymczasowego wraz ze ścieżką do głównego katalogu
     * thumbnaili na podstawie konfiguracji.
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
        chmod($destination, 0777);
    }

}
