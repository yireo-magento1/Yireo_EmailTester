<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

/**
 * Class Yireo_EmailTester_Block_Form_Abstract
 */
class Yireo_EmailTester_Block_Form_Abstract extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->helper = Mage::helper('emailtester');
    }

    /**
     * Get an array of all options defined in the extension settings
     *
     * @param null $type
     *
     * @return array|bool
     */
    public function getCustomOptions($type = null)
    {
        $customOptions = Mage::getStoreConfig('emailtester/settings/custom_' . $type);
        if (empty($customOptions)) {
            return false;
        }

        $options = array();
        $customOptions = explode(',', $customOptions);
        foreach ($customOptions as $customOption) {
            $customOption = (int) trim($customOption);
            if ($customOption > 0) {
                $options[] = $customOption;
            }
        }

        return $options;
    }

    /**
     * Get the current store
     *
     * @return int|mixed
     * @throws Exception
     */
    public function getStoreId()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if (!$storeId > 0) {
            $storeId = Mage::getSingleton('adminhtml/session')->getData('emailtester.store');
        }

        return $storeId;
    }
}
