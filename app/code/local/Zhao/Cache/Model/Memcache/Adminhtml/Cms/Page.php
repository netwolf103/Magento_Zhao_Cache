<?php
class Jeulia_Cache_Model_Memcache_Adminhtml_Cms_Page extends Jeulia_Cache_Model_Memcache_Cms_Page_View
{
	public function ClearCache(Varien_Event_Observer $observer)
	{
		$page = $observer->getEvent()->getPage();

		$this->ClearPageCache($page->getId());
	}

	private function ClearPageCache($id)
	{
		$cacheConfig = 'page-'. $id . '.config';

		$keys = $this->_cache->get( $this->getCacheId($cacheConfig) );

		if( $keys ) {
			$keys = unserialize($keys);
			array_push($keys, $this->getCacheId($cacheConfig));

			$this->remove($keys);
		}
	}
}