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
 * Magento mage cache abstract
 *
 * @category   Zhao_Cache
 * @author     Zhang Zhao<303685256@qq.com>
 */
class Zhao_Cache_Model_Mage_Abstruct
{
    // Cache root path name
    const CACHE_ROOT_DIR        = 'cache';
    // Cache html path name
    const CACHE_HTML_DIR        = 'html';
    // Ext php script name
    const MAIN_PHP_NAME         = 'cache.php';
    // htaccess name
    const HTACCESS_FILE_NAME    = '.htaccess';
    const APP_HTACCESS_FILE_NAME= 'app.htaccess.lock';

    const MAGENTO_FILE_NAME     = 'Mage.xml';

    const LIFE_TIME             = 'zhaocache/config/lifetime';

    /**
     * Cache instance
     *
     * @var null|Varien_Cache_Core
     */
    protected $cacheInstance;

    /**
     * Cache tags
     *
     * @var null|array
     */
    protected $cacheTags;

    /**
     * Varien file IO
     *
     * @var null|Varien_Io_File
     */
    protected $_fileIo;
    
    /**
     * Constructor
     * Initialize resources.
     */
    public function __construct()
    {
        $this->cacheRootDir = MAGENTO_ROOT .'/'. self::CACHE_ROOT_DIR;
        $this->cacheDir     = $this->cacheRootDir .'/'. self::CACHE_HTML_DIR;

        $this->_getCacheInstance();
        
        $this->cacheTags = array('Zhao_CACHE_HTML');

        if ($cacheId = $this->getCacheId()) {
             $this->cacheTags = array_merge($this->cacheTags, $this->getCacheTag($cacheId));
        }
    }

    /**
     *
     */
    protected function getAppHtaccess()
    {
        if( @file_exists(MAGENTO_ROOT.'/.htaccess') ) {
            return @file_get_contents(MAGENTO_ROOT.'/.htaccess');
        }

        return false;
    }
    
    /**
     * The full cache root path
     *
     * @return string
     */
    protected function getCacheRootDir()
    {
        return $this->cacheRootDir;
    }
    
    /**
     * The full .htaccess file path.
     *
     * @return string
     */
    protected function getHtaccessFile()
    {
        return $this->cacheRootDir .'/'. self::HTACCESS_FILE_NAME;
    }

    /**
     * The source site .htaccess file path.
     */
    protected function getAppHtaccessFile()
    {
        return $this->cacheRootDir .'/'. self::APP_HTACCESS_FILE_NAME;
    }

    /**
     * The full magento.xml file path.
     *
     * @return string
     */
    protected function getMagentoFile()
    {
        return $this->cacheRootDir .'/'. self::MAGENTO_FILE_NAME;
    }

    /**
     * The Mage.xml path.
     *
     * @return string
     */
    protected function getMageXmlFile()
    {
        return $this->getEtcDir() . '/Mage.xml';
    }
    
    /**
     * The module etc path.
     *
     * @return string
     */
    protected function getEtcDir()
    {
        return Mage::getModuleDir('etc', 'Zhao_Cache');
    }
    
    /**
     * The module code.xml path.
     *
     * @return string
     */
    protected function getEtcCodeXmlFile()
    {
        return $this->getEtcDir() . '/code.xml';
    }
    
    /**
     * The user agent is mobile device.
     *
     * @return bool;
     */
    protected function is_mobile()
    {
        return Zend_Http_UserAgent_Mobile::match( Mage::helper('core/http')->getHttpUserAgent(), $_SERVER );
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function getRequest()
    {
        return Mage::app()->getRequest();
    }
    
    /**
     * Retrive layout object
     *
     * @return Mage_Core_Model_Layout
     */
    protected function getLayout()
    {
        return Mage::app()->getLayout();
    }

    /**
     * Current request url
     *
     * @return string
     */
    protected function getCurrentUrl()
    {
        return Mage::helper('core/url')->getCurrentUrl();
    }
    
    protected function cacheEnabled()
    {
        return Mage::getStoreConfig($this::CONFIG_PATH_ENABLED);
    }

    /**
     * Current cache file name.
     */
    protected function cacheFile()
    {

        $url = explode('?',  $this->getCurrentUrl(), 2);

        $cacheId = md5($url[0]);
        if( isset($url[1]) && !empty($url[1]) ) {
            $cacheId .= '_' . md5($url[1]);
        }

        $filename = $this->is_mobile() ? $cacheId.'_mobile' : $cacheId;

        $this->filename = 'Zhao_CACHE_HTML' .'_'. $filename .'_'. Mage::app()->getStore()->getCurrentCurrencyCode() .'_'. Mage::app()->getStore()->getCode();

        return $this;
    }

    /**
     * Current cache data.
     */
    protected function cacheData()
    {
        $output = $this->getLayout()->getOutput();

        $this->output = gzcompress($output);
        
        return $this;
    }

    /**
     * Pre Write cache.
     */
    protected function saveCache()
    {
        if( empty($this->filename) ) {
            return $this;
        }

        $this->_getCacheInstance()->save($this->output, $this->filename, $this->cacheTags, $this->getLifeTime());

        return $this;
    }

    protected function getLifeTime()
    {
       $lifetime = Mage::getStoreConfig(self::LIFE_TIME);

       $lifetime = (int) $lifetime;

       if( !$lifetime ) {
            $lifetime = 3600;
       }

       return $lifetime;
    }
    
    /**
     * Write cache.
     */
    protected function write()
    {
        if( $this->cacheEnabled() && !$this->isElb() ) {
            $this->cacheFile()->cacheData()->saveCache();
        }
    }

    public function cache()
    {
        if (!$this->getCacheId()) {
            return false;
        }

        $this->write();
    }

    /**
     * The request is Elb HealthChecker.
     */
    protected function isElb()
    {
        $userAgent = Mage::helper('core/http')->getHttpUserAgent();

        $bool = stristr($userAgent, 'ELB-HealthChecker');

        return (bool) $bool;
    }
    
    /**
     * Delete cache.
     */
    protected function clean($id)
    {
        return $this->_getCacheInstance()->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, $this->getCacheTag($id));
    }

    protected function getCacheId()
    {
        return false;    
    }

    protected function getCacheTag($id)
    {
        return array(sprintf('Zhao_CACHE_HTML_%s_%s', $this::CACHE_TYPE, $id));
    }

    /**
     * Get file IO
     *
     * @return Varien_Io_File
     */
    protected function _getFileIo()
    {
        if ($this->_fileIo === null) {
            $this->_fileIo = new Varien_Io_File();
        }
        return $this->_fileIo;
    }

    /**
     * Get file IO
     *
     * @return Varien_Io_File
     */
    protected function _getCacheInstance()
    {
        if ($this->cacheInstance === null) {
            $this->cacheInstance = Mage::app()->getCache();
        }
        return $this->cacheInstance;
    }
}