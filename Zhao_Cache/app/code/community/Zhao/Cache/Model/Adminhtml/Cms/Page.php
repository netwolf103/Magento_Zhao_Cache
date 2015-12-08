<?php
class Zhao_Cache_Model_Adminhtml_Cms_Page extends Zhao_Cache_Model_Cms_Page_View
{
	public function ClearCache(Varien_Event_Observer $observer)
	{
		$page = $observer->getEvent()->getPage();

		$this->ClearPageCache($page->getId());
	}

	private function ClearPageCache($id)
	{
		$cacheConfig = 'page-'. $id . '.config';

		if( is_file($this->cacheDir .'/'. $cacheConfig) ) {
			$cacheList = @include($this->cacheDir .'/'. $cacheConfig);
			array_push($cacheList, $cacheConfig);

			$this->remove($cacheList);
		}
	}
}