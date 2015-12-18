<?php
class Zhao_Cache_Model_Cms_Page_View extends Zhao_Cache_Model_Abstract
{
	protected  $config_path = 'zhaocache/config/cmspage_enabled';

	public function __construct()
	{
		parent::__construct();

		if ($pageId = $this->getPageId()) {
			$this->cacheConfig = $this->cacheDir . '/page-' . $pageId . '.config';
		}
	}

	public function cache()
	{

        if (!$this->getPageId()) {
            return false;
        }

		$this->write();
	}	

	private function getPageId()
	{
		$page = Mage::getSingleton('cms/page');

        if (!$page->getId()) {
            return false;
        }

		return $page->getId();
        
	}
}