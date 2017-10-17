<?php
class Jeulia_Cache_Model_Memcache_Adminhtml_Catalog_Category extends Jeulia_Cache_Model_Memcache_Catalog_Category_View
{
	public function ClearCache(Varien_Event_Observer $observer)
	{
		$category = $observer->getEvent()->getCategory();

		$this->ClearCategoryCache($category->getId());
	}	

	private function ClearCategoryCache($id)
	{
		$cacheConfig = 'category-'. $id . '.config';

		$keys = $this->_cache->get( $this->getCacheId($cacheConfig) );

		if( $keys ) {
			$keys = unserialize($keys);
			array_push($keys, $this->getCacheId($cacheConfig));

			$this->remove($keys);
		}
	}
}