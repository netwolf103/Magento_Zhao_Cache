<?php
class Jeulia_Cache_Model_Memcache_Adminhtml_System_Config extends Jeulia_Cache_Model_Memcache_Abstruct
{
	// code xml file
	const CODE_XML_NAME = 'code.xml';
	
	/**
	 * Save config.
	 */
	public function save()
	{
		$groups = $this->getRequest()->getPost('groups');

		$enabled = $groups['config']['fields']['cmspage_enabled']['value'] ||
			$groups['config']['fields']['catalog_category_enabled']['value'] ||
			$groups['config']['fields']['catalog_product_enabled']['value'];

		if( !$enabled ) {
			$this->reset();
		} else {
			$this->setCode($enabled);
			$this->chageAppHtaccess($enabled);			
		}
	}

	public function reset()
	{
		if( file_exists($this->getAppHtaccessFile()) ) {
			$config = file_get_contents($this->getAppHtaccessFile());
			file_put_contents(MAGENTO_ROOT .'/.htaccess', $config);
		}

		@unlink(MAGENTO_ROOT .'/'. self::MAIN_PHP_NAME);
		@unlink($this->getAppHtaccessFile());
		@unlink($this->getMagentoFile());
		@unlink($this->lockFile());
	}
	
	protected function setCode()
	{
		$fileName = MAGENTO_ROOT .'/'. self::MAIN_PHP_NAME;

			
		$codeXmlConfig = new Varien_Simplexml_Config( $this->getEtcDir() .'/'. self::CODE_XML_NAME );
		
		$host = $port = '';
		if( $options = Mage::getStoreConfig(self::CONFIG_PATH_OPTIONS) ) {
			list($host, $port) =  explode(':', $options);
		}

		if( !$host || !$port ) {
			return false;
		}

		$code = "<?php\r\ndefine('HOST', '{$host}');\r\ndefine('PORT', '{$port}');\r\n" .$codeXmlConfig->getNode('global/code/memcache'). "\r\n?>";
		file_put_contents($fileName, $code);
	}

	protected function chageAppHtaccess()
	{
		$fileName = MAGENTO_ROOT .'/.htaccess';

		if( !file_exists($fileName) || file_exists($this->lockFile()) ) {
			return;
		}

		$config = file_get_contents($fileName);
		//$config = str_replace('index.php', self::MAIN_PHP_NAME, $config);
		$config = str_replace('DirectoryIndex index.php', sprintf('DirectoryIndex %s index.php', self::MAIN_PHP_NAME), $config);
		$config = str_replace('RewriteRule .* index.php', sprintf('RewriteRule .* %s', self::MAIN_PHP_NAME), $config);
		file_put_contents($fileName, $config);
		touch($this->lockFile());

	}

	protected function lockFile()
	{
		return $this->cacheRootDir .'/htaccess.lock';	
	}

}