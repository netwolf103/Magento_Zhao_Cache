<?php
class Zhao_Cache_Model_Catalog_Category_View extends Zhao_Cache_Model_Abstruct
{
	protected  $config_path = 'zhaocache/config/catalog_category_enabled';

	public function __construct()
	{
		parent::__construct();

		if ($categoryId = $this->getCategoryId()) {
			$this->cacheConfig = $this->cacheDir . '/category-' . $categoryId . '.config';
		}
	}

	public function cache()
	{

        if (!$this->getCategoryId()) {
            return false;
        }

		$this->write();
	}
	
	private function getCategoryId()
	{
        return (int) $this->getRequest()->getParam('id', false);
        
	}
}