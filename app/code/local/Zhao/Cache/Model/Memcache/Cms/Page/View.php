<?php
class Jeulia_Cache_Model_Memcache_Cms_Page_View extends Jeulia_Cache_Model_Memcache_Abstruct
{
	protected  $config_path = 'jeuliacache/config/cmspage_enabled';

	public function __construct()
	{
		parent::__construct();

		if ($pageId = $this->getPageId()) {
			$this->cacheConfig = 'page-' . $pageId . '.config';
		}
	}

	public function cache()
	{
        if (!$this->getPageId()) {
            return false;
        }

		$this->write();
	}	

	protected function getPageId()
	{
		$page = Mage::getSingleton('cms/page');

        if (!$page->getId()) {
            return false;
        }

		return $page->getId();
        
	}
}