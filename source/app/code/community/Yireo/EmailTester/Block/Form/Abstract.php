<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_EmailTester_Block_Form_Abstract extends Mage_Adminhtml_Block_Widget_Container
{
    public function getCustomOptions($type = null)
    {
        $customOptions = Mage::getStoreConfig('emailtester/settings/custom_'.$type);
        if(empty($customOptions)) return false;

        $options = array();
        $customOptions = explode(',', $customOptions);
        foreach($customOptions as $customOption) {
            $customOption = (int)trim($customOption);
            if($customOption > 0) {
                $options[] = $customOption;
            }
        }

        return $options;
    }

    public function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        if(!$storeId > 0) {
            $storeId = Mage::getSingleton('adminhtml/session')->getData('emailtester.store');
        }

        return $storeId;
    }
}
