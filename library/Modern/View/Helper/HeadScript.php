<?php

/** @see Zend_View_Helper_HeadScript */
require_once 'Zend/View/Helper/HeadScript.php';

class Modern_View_Helper_HeadScript extends Zend_View_Helper_HeadScript {

	protected $_minify = false;
	protected $_minifyFilter = 'Minify_UglifyJs';
	protected $_minifyFilterNamespace = 'Zend_Filter';
	protected $_merge = false;

	protected $_rootPath = '';
	protected $_basePath = '';
	protected $_varPath = '';
	protected $_autoRefresh = false;

	/**
	 * Ustawia konfigurację helpera.
	 *
	 * @param array|Zend_Config $options
	 */
	public function setOptions($options) {
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		}

		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (method_exists($this, $method)) {
				$this->{$method}($value);
			}
		}
	}

	public function setMerge($flag) {
		$this->_merge = (bool) $flag;

		if ($this->getMerge()) {
			$this->setMinify(true);
		}
		return $this;
	}

	public function getMerge() {
		return $this->_merge;
	}

	public function setMinify($flag) {
		$this->_minify = (bool) $flag;
		return $this;
	}

	public function getMinify() {
		return $this->_minify;
	}

	public function setMinifyFilter($filter) {
		$this->_minifyFilter = $filter;
		return $this;
	}

	public function getMinifyFilter() {
		return $this->_minifyFilter;
	}

	public function setMinifyFilterNamespace($namespace) {
		$this->_minifyFilterNamespace = $namespace;
		return $this;
	}

	public function getMinifyFilterNamespace() {
		return $this->_minifyFilterNamespace;
	}

	public function setRootPath($path) {
		$this->_rootPath = rtrim($path, '/');
		return $this;
	}

	public function getRootPath() {
		return $this->_rootPath;
	}

	public function setBasePath($path) {
		$this->_basePath = rtrim($path, '/') . '/';
		return $this;
	}

	public function getBasePath() {
		return $this->_basePath;
	}

	public function setVarPath($path) {
		$this->_varPath = rtrim($path, '/') . '/';
		return $this;
	}

	public function getVarPath() {
		return $this->_varPath;
	}

	public function setAutoRefresh($flag) {
		$this->_autoRefresh = (bool) $flag;
		return $this;
	}

	public function getAutoRefresh() {
		return $this->_autoRefresh;
	}

	/**
	 * Retrieve string representation
	 *
	 * @param  string|int $indent
	 * @return string
	 */
	public function toString($indent = null) {
		$indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();

		if ($this->view) {
			$useCdata = $this->view->doctype()->isXhtml() ? true : false;
		} else {
			$useCdata = $this->useCdata ? true : false;
		}
		$escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
		$escapeEnd = ($useCdata) ? '//]]>' : '//-->';

		$items = array();
		$this->getContainer()->ksort();

		$itemsToMerge = array();

		foreach ($this as $item) {
			if (!$this->_isValid($item)) {
				continue;
			}

			if ($item->isFile && $this->getMinify()) {
				$this->_minify($item);
			}

			if($item->isFile && $this->getMerge()) {
				$itemsToMerge[] = $item;
				continue;
			}

			$items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
		}

		if(count($itemsToMerge)) {
			$items[] = $this->itemToString($this->_merge($itemsToMerge), $indent, $escapeStart, $escapeEnd);
		}

		$return = implode($this->getSeparator(), $items);
		return $return;
	}

	/**
	 * Create data item containing all necessary components of script
	 *
	 * @param  string $type
	 * @param  array $attributes
	 * @param  string $content
	 * @return stdClass
	 */
	public function createData($type, array $attributes, $content = null) {
		$data = parent::createData($type, $attributes, $content);
		$data->isFile = false;

		if (isset($data->attributes['src'])) {
			// określenie czy src wskazuje na lokalny plik
			$data->isFile = !strpos($data->attributes['src'], ':');
		}

		return $data;
	}

	/**
	 * Kompresja plików JavaScript.
	 *
	 * @param stdClass $item
	 * @return stdClass
	 */
	protected function _minify(stdClass $item) {

		$item->filename = implode('/', array_diff(
			explode('/', $item->attributes['src']), explode('/', $this->getBasePath())
		));

		$baseFile = $this->getRootPath() . $this->getBasePath() . $item->filename;
		if(!is_readable($baseFile)) {
			$item->isFile = false;
			return $item;
		}

		$search = (false === strpos($baseFile, '.min.')) ? '.js' : '.min.js';
		$item->filename = str_replace($search, '.min.js', $item->filename);

		$item->attributes['src'] = $this->getVarPath() . $item->filename;
		$item->filepath = $this->getRootPath() . $this->getVarPath() . $item->filename;

		if(
			!is_file($item->filepath) ||
			($this->getAutoRefresh() && filemtime($baseFile) != filemtime($item->filepath))
		) {
			// obsługa już skompresowanych plików bazowych (suffix .min.js)
			if(false !== strpos($baseFile, '.min.')) {
				copy($baseFile, $item->filepath);
				chmod($item->filepath, 0777);
				if($this->getAutoRefresh()) {
					touch($item->filepath, filemtime($baseFile));
				}
				return $item;
			}

			// minify & save
			try {
				$content = Zend_Filter::filterStatic(
					file_get_contents($baseFile), $this->getMinifyFilter(), array(), $this->getMinifyFilterNamespace()
				);
				$dir = dirname($item->filepath);
				if(!is_dir($dir)) {
					umask(0);
					mkdir($dir, 0777, true);
				}
				file_put_contents($item->filepath, $content);
				chmod($item->filepath, 0777);
				if($this->getAutoRefresh()) {
					touch($item->filepath, filemtime($baseFile));
				}
			} catch(Exception $e) {
				// metoda __toString() nie może rzucać wyjątków
				trigger_error($e->getMessage(), E_USER_WARNING);
			}
		}

		return $item;
	}

	/**
	 * Łączy pliki $items w jeden plik.
	 *
	 * @param array $items
	 * @return stdClass
	 */
	protected function _merge(array $items) {
		// suma kontrolna pliku wynikowego
		$sum = '';
		foreach($items as $item) {
			$sum .= $item->attributes['src'];

			if($this->getAutoRefresh()) {
				$sum .= '?' . filemtime($item->filepath);
			}
		}

		$filename = md5($sum) . '.js';
		$mergeFile = $this->getRootPath() . $this->getVarPath() . $filename;

		if(!is_file($mergeFile)) {
			$content = '';
			foreach($items as $item) {
				$content .= file_get_contents($item->filepath) . ';' . PHP_EOL;
			}
			file_put_contents($mergeFile, $content);
			chmod($mergeFile, 0777);
		}

		$item = $this->createData('text/javascript', array(
			'src' => $this->getVarPath() . $filename
		));
		$item->isFile = false; // blokada minify dla złączonego pliku
		return $item;
	}

}
