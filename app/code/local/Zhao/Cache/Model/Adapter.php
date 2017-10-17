<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @version    $Id: Page.php 2017-10-14 03:03:13 Zhao<303685256@qq.com> $
 */

/**
 * Adapter class
 *
 * @category   Zhao_Cache
 * @author     Zhang Zhao<303685256@qq.com>
 */
class Zhao_Cache_Model_Adapter
{
    const CONFIG_PATH_ADAPTER       = 'zhaocache/config/adapter';

    const EVENT_CMS_PAGE_WRITE      = 'controller_action_layout_render_before_cms_page_view';
    const EVENT_HOME_PAGE_WRITE     = 'controller_action_layout_render_before_cms_index_index';
    const EVENT_PRODUCT_WRITE       = 'controller_action_layout_render_before_catalog_product_view';
    const EVENT_CATEGORY_WRITE      = 'controller_action_layout_render_before_catalog_category_view';


    protected $_adapter;

    /**
     * Constructor
     * Initialize resources.
     */
    public function __construct(){}

    protected function getAdapterSource()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_ADAPTER) ? Mage::getStoreConfig(self::CONFIG_PATH_ADAPTER) : 'files'; 
    }

    protected function getAdapter($className)
    {
        return Mage::getModel( sprintf("zhaocache/%s_{$className}", $this->getAdapterSource(), $className) );
    }

    protected function getAdminAdapter($className)
    {
        return Mage::getModel( sprintf("zhaocache/%s_adminhtml_{$className}", $this->getAdapterSource(), $className) );
    }

    public function cache(Varien_Event_Observer $observer)
    {
        list(, $_name) = (explode('_before_', $observer->getEvent()->getName()));

        if( !$_name ) {
            return false;
        }

        if( $_object = $this->getAdapter($_name) ) {
            $_object->cache();
        }
    }

    public function ClearCache(Varien_Event_Observer $observer)
    {
        list($_name) = (explode('_prepare', $observer->getEvent()->getName()));

        if( !$_name ) {
            return false;
        }

        if( $_object = $this->getAdminAdapter($_name) ) {
            $_object->ClearCache($observer);
        }
    }

    public function config(Varien_Event_Observer $observer)
    {
        if( $_object = $this->getAdminAdapter('system_config') ) {
            $_object->save($observer);
        }       
    }
}