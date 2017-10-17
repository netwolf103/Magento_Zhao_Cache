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
 * Magento mage cache config
 *
 * @category   Zhao_Cache
 * @author     Zhang Zhao<303685256@qq.com>
 */
class Zhao_Cache_Model_Mage_Adminhtml_System_Config extends Zhao_Cache_Model_Mage_Abstruct
{
    // code xml file
    const CODE_XML_NAME = 'code.xml';
    
    /**
     * Save config.
     */
    public function save()
    {
        $groups = $this->getRequest()->getPost('groups');

        $enabled = $groups['config']['fields']['cmspage_enabled']['value'] ||
            $groups['config']['fields']['catalog_category_enabled']['value'] ||
            $groups['config']['fields']['catalog_product_enabled']['value'];

        if( !$enabled ) {
            $this->reset();
        } else {

            if( !$this->_getFileIo()->fileExists($this->getMagentoFile()) ) {
                $this->initialize();
                $this->setCode($enabled);
                $this->chageAppHtaccess($enabled);
            }
        }
    }

    public function reset()
    {
        $_fileIo = $this->_getFileIo();

        if( $_fileIo->fileExists($this->getAppHtaccessFile()) ) {

            @file_put_contents( MAGENTO_ROOT .'/.htaccess', $_fileIo->read($this->getAppHtaccessFile()) );
        }

        $_fileIo->rm(MAGENTO_ROOT .'/'. self::MAIN_PHP_NAME);

        $_fileIo->rmdir($this->cacheRootDir, true);
    }

    protected function initialize()
    {
        $_fileIo = $this->_getFileIo();

        if( !is_dir($this->cacheRootDir) ) {
            $_fileIo->mkdir($this->cacheRootDir);
        }

        if( !$_fileIo->fileExists($this->getHtaccessFile()) ) {
            $codeXmlConfig = new Varien_Simplexml_Config($this->getEtcCodeXmlFile());

            @file_put_contents($this->getHtaccessFile(), $codeXmlConfig->getNode('global/htaccess'));
        }

        if( !$_fileIo->fileExists($this->getMagentoFile()) ) {

            Mage::getConfig()->setNode('default/store/value', Mage::app()->getDefaultStoreView()->getCode());

            $_fileIo->write($this->getMagentoFile(), Mage::getConfig()->getXmlString());
        }

        if( !$_fileIo->fileExists($this->getAppHtaccessFile()) ) {

            $_fileIo->write($this->getAppHtaccessFile(), $this->getAppHtaccess());
        }
    }
    
    protected function setCode()
    {
        $fileName = MAGENTO_ROOT .'/'. self::MAIN_PHP_NAME;
            
        $codeXmlConfig = new Varien_Simplexml_Config( $this->getEtcDir() .'/'. self::CODE_XML_NAME );

        $code = "<?php\r\n" .$codeXmlConfig->getNode('global/code/mage'). "\r\n?>";

        $this->_getFileIo()->write($fileName, $code);
    }

    protected function chageAppHtaccess()
    {
        $_fileIo = $this->_getFileIo();

        $fileName = MAGENTO_ROOT .'/.htaccess';

        if( !$_fileIo->fileExists($fileName) || !$_fileIo->isWriteable($fileName) ) {
            return;
        }

        $config = @file_get_contents($fileName);
    
        $config = str_replace('DirectoryIndex index.php', sprintf('DirectoryIndex %s index.php', self::MAIN_PHP_NAME), $config);
        $config = str_replace('RewriteRule .* index.php', sprintf('RewriteRule .* %s', self::MAIN_PHP_NAME), $config);

        $_fileIo->write($fileName, $config);

    }

}