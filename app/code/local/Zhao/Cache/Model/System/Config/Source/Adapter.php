<?php

class Zhao_Cache_Model_System_Config_Source_Adapter
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'memcache',
                'label' => Mage::helper('adminhtml')->__('Memcache')
            ),
            array(
                'value' => 'mage',
                'label' => Mage::helper('adminhtml')->__('Mage')
            ),
        );
    }
}
