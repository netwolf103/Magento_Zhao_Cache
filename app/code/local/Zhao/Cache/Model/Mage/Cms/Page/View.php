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
 * Magento mage cache for Cms Page
 *
 * @category   Zhao_Cache
 * @author     Zhang Zhao<303685256@qq.com>
 */
class Zhao_Cache_Model_Mage_Cms_Page_View extends Zhao_Cache_Model_Mage_Abstruct
{
    const CACHE_TYPE            = 'PAGE';
    const CONFIG_PATH_ENABLED   = 'zhaocache/config/cmspage_enabled';

    /**
     * Get the page id.
     *
     * @return intval
     */
    protected function getCacheId()
    {
        $page = Mage::getSingleton('cms/page');

        if (!$page->getId()) {
            return false;
        }

        return $page->getId();
        
    }
}