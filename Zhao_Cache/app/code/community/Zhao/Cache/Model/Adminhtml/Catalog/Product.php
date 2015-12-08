<?php
class Zhao_Cache_Model_Adminhtml_Catalog_Product extends Zhao_Cache_Model_Catalog_Product_View
{
	public function ClearCache(Varien_Event_Observer $observer)
	{
		$product = $observer->getEvent()->getProduct();
		
		$this->ClearProductCache($product->getId());

		$categoryIds = $product->getCategoryIds();
		foreach($categoryIds as $categoryId) {

			$this->ClearCategoryCache($categoryId);
		}

		$this->remove($filenames);

	}

	private function ClearProductCache($id)
	{
		$cacheConfig = 'product-'. $id . '.config';

		if( is_file($this->cacheDir .'/'. $cacheConfig) ) {
			$cacheList = @include($this->cacheDir .'/'. $cacheConfig);
			array_push($cacheList, $cacheConfig);

			$this->remove($cacheList);
		}
	}

	private function ClearCategoryCache($id)
	{
		$cacheConfig = 'category-'. $id . '.config';

		if( is_file($this->cacheDir .'/'. $cacheConfig) ) {
			$cacheList = @include($this->cacheDir .'/'. $cacheConfig);
			array_push($cacheList, $cacheConfig);

			$this->remove($cacheList);
		}
	}
}