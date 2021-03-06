<?php
class Jeulia_Cache_Model_Memcache_Catalog_Product_View extends Jeulia_Cache_Model_Memcache_Abstruct
{
	protected  $config_path = 'jeuliacache/config/catalog_product_enabled';

	public function __construct()
	{
		parent::__construct();

		if ( $productId = $this->getProductId() ) {
			$this->cacheConfig = 'product-' . $productId . '.config';
		}
	}

	public function cache()
	{
        if (!$this->getProductId()) {
            return false;
        }
		
		$this->write();
	}
	
	private function getProductId()
	{
		$productId  = (int) $this->getRequest()->getParam('id');

		$product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);

		 return $product->getId() ? $product->getId() : false;
        
	}
}