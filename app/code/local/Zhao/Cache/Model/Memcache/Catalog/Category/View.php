<?php
class Jeulia_Cache_Model_Memcache_Catalog_Category_View extends Jeulia_Cache_Model_Memcache_Abstruct
{
	protected  $config_path = 'jeuliacache/config/catalog_category_enabled';

	public function __construct()
	{
		parent::__construct();

		if ($categoryId = $this->getCategoryId()) {
			$this->cacheConfig = 'category-' . $categoryId . '.config';
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