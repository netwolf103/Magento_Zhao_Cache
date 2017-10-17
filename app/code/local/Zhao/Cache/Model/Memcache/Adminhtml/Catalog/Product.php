<?php
class Jeulia_Cache_Model_Memcache_Adminhtml_Catalog_Product extends Jeulia_Cache_Model_Memcache_Catalog_Product_View
{
	public function ClearCache(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getProduct();
		
		$this->ClearProductCache($product->getId());

		$categoryIds = $product->getCategoryIds();
		foreach($categoryIds as $categoryId) {

			$this->ClearCategoryCache($categoryId);
		}

	}

	private function ClearProductCache($id)
	{
		$cacheConfig = 'product-'. $id . '.config';

		$keys = $this->_cache->get( $this->getCacheId($cacheConfig) );

		if( $keys ) {
			$keys = unserialize($keys);
			array_push($keys, $this->getCacheId($cacheConfig));

			$this->remove($keys);
		}
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