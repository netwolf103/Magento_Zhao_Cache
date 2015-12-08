<?php
class Zhao_Cache_Model_Adminhtml_Catalog_Category extends Zhao_Cache_Model_Catalog_Category_View
{
	public function ClearCache(Varien_Event_Observer $observer)
	{
		$category = $observer->getEvent()->getCategory();

		$this->ClearCategoryCache($category->getId());
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