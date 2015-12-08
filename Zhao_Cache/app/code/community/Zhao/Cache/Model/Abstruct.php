<?php
class Zhao_Cache_Model_Abstruct
{
	// Cache root path name
	const CACHE_ROOT_DIR		= 'cache';
	// Cache html path name
	const CACHE_HTML_DIR		= 'html';
	// Ext php script name
	const MAIN_PHP_NAME			= 'cache.php';
	// htaccess name
	const HTACCESS_FILE_NAME	= '.htaccess';
	const APP_HTACCESS_FILE_NAME	= 'app.htaccess.lock';

	const MAGENTO_FILE_NAME		= 'Mage.xml';

	protected $cacheConfig = '';
	
	/**
	 * Constructor
	 * Initialize resources.
	 */
	public function __construct()
	{
		$this->cacheRootDir = MAGENTO_ROOT .'/'. self::CACHE_ROOT_DIR;
		$this->cacheDir		= $this->cacheRootDir .'/'. self::CACHE_HTML_DIR;
		
		if( !is_dir($this->cacheDir) ) {
			@mkdir($this->cacheDir, 0777, true);
		}

		if( !file_exists($this->getHtaccessFile()) ) {
			$codeXmlConfig = new Varien_Simplexml_Config($this->getEtcCodeXmlFile());

			file_put_contents($this->getHtaccessFile(), $codeXmlConfig->getNode('global/htaccess'));
		}

		if( !file_exists($this->getMagentoFile()) ) {

			file_put_contents($this->getMagentoFile(), Mage::getConfig()->getXmlString());
		}

		if( !file_exists($this->getAppHtaccessFile()) ) {

			file_put_contents($this->getAppHtaccessFile(), $this->getAppHtaccess());
		}
	}

	/**
	 *
	 */
	protected function getAppHtaccess()
	{
		if( file_exists(MAGENTO_ROOT.'/.htaccess') ) {
			return file_get_contents(MAGENTO_ROOT.'/.htaccess');
		}

		return false;
	}
	
	/**
	 * The full cache root path
	 *
	 * @return string
	 */
	protected function getCacheRootDir()
	{
		return $this->cacheRootDir;
	}
	
	/**
	 * The full html cache path.
	 *
	 * @return string
	 */
	protected function getCacheHtmlDir()
	{
		return $this->cacheRootDir .'/'. self::CACHE_HTML_DIR;
	}
	
	/**
	 * The full .htaccess file path.
	 *
	 * @return string
	 */
	protected function getHtaccessFile()
	{
		return $this->cacheRootDir .'/'. self::HTACCESS_FILE_NAME;
	}

	/**
	 * The source site .htaccess file path.
	 */
	protected function getAppHtaccessFile()
	{
		return $this->cacheRootDir .'/'. self::APP_HTACCESS_FILE_NAME;
	}

	/**
	 * The full magento.xml file path.
	 *
	 * @return string
	 */
	protected function getMagentoFile()
	{
		return $this->cacheRootDir .'/'. self::MAGENTO_FILE_NAME;
	}

	/**
	 * The Mage.xml path.
	 *
	 * @return string
	 */
	protected function getMageXmlFile()
	{
		return $this->getEtcDir() . '/Mage.xml';
	}
	
	/**
	 * The module etc path.
	 *
	 * @return string
	 */
	protected function getEtcDir()
	{
		return Mage::getModuleDir('etc', 'Zhao_Cache');
	}
	
	/**
	 * The module code.xml path.
	 *
	 * @return string
	 */
	protected function getEtcCodeXmlFile()
	{
		return $this->getEtcDir() . '/code.xml';
	}
	
	/**
	 * The user agent is mobile device.
	 *
	 * @return bool;
	 */
	protected function is_mobile()
	{
		return Zend_Http_UserAgent_Mobile::match( Mage::helper('core/http')->getHttpUserAgent(), $_SERVER );
	}

	/**
	 * Retrieve request object
	 *
	 * @return Mage_Core_Controller_Request_Http
	 */
	protected function getRequest()
	{
		return Mage::app()->getRequest();
	}
	
    /**
     * Retrive layout object
     *
     * @return Mage_Core_Model_Layout
     */
	protected function getLayout()
	{
		return Mage::app()->getLayout();
	}

    /**
     * Current request url
     *
     * @return string
     */
	protected function getCurrentUrl()
	{
		return Mage::helper('core/url')->getCurrentUrl();
	}
	
	protected function cacheEnabled()
	{
		return Mage::getStoreConfig($this->config_path);
	}

	/**
	 * File last modify time.
	 *
	 * @return initval
	 */
	protected function filemtime($filename)
	{
		if( !file_exists($this->cacheDir . '/' . $filename) ) {
			return 0;
		}

		return filemtime($this->cacheDir . '/' . $filename);
	}

	/**
	 * Current cache file name.
	 */
	protected function cacheFile()
	{

		$filename = $this->is_mobile() ? md5($this->getCurrentUrl()).'_mobile' : md5($this->getCurrentUrl());

		$this->filename = $filename .'_'. Mage::app()->getStore()->getCurrentCurrencyCode();
		
		return $this;
	}

	/**
	 * Current cache data.
	 */
	protected function cacheData()
	{
		$output = $this->getLayout()->getOutput();
		$output = str_replace('https://', '//', $output);
		$output = str_replace('http://', '//', $output);

		$this->output = $output;
		
		return $this;
	}

	/**
	 * The cache config data.
	 */
	protected function cacheConfig()
	{
		if( !isset($this->filename) || empty($this->filename) ) {
			return $this;
		}

		if( empty($this->cacheConfig) ) {
			return $this;
		}

		if( !is_file($this->cacheConfig) ) {
			$files = array($this->filename);
		} else {
			$files = @include($this->cacheConfig);
			array_push($files, $this->filename);

			$files = array_unique($files);			
		}

		file_put_contents( $this->cacheConfig, "<?php\r\nreturn " . var_export($files, true) . ";\r\n?>" );

		return $this;
	}

	/**
	 * Pre Write cache.
	 */
	protected function saveCache()
	{
		$this->cached = false;

		if( empty($this->filename) ) {
			return $this;
		}

		if( !file_put_contents( $this->cacheDir . '/' . $this->filename, $this->output ) ) {
			return $this;
		}

		$this->cached = true;

		return $this;
	}
	
	/**
	 * The append cache file list.
	 */
	protected function appendCache()
	{
		if( !isset($this->cached) || !$this->cached ) {
			return $this;
		}

		return $this->cacheConfig();
	}
	
	/**
	 * Write cache.
	 */
	protected function write()
	{
		if( $this->cacheEnabled() ) {

			$this->cacheFile()->cacheData()->saveCache()->appendCache();
		}
	}
	
	/**
	 * Read cache.
	 */
	protected function read($filename)
	{
		return file_get_contents( $this->cacheDir . '/' . $filename );
	}

	/**
	 * Delete cache.
	 */
	protected function remove($filenames)
	{
		if( !is_array() )
			$filenames = (array)$filenames;

		foreach($filenames as $filename) {
			$this->delete($filename);
			$this->delete($filename.'_mobile');
		}
	}
	
	/**
	 * Delete cache.
	 */
	protected function delete($filename)
	{
		return @unlink( $this->cacheDir . '/' . $filename );
	}
	
	/**
	 * Rmdir cache directory.
	 */
	protected function rmdir($dirname)
	{
		$dirname = $this->cacheDir . '/' . $dirname;

		return Varien_Io_File::rmdirRecursive($dirname);
	}
}