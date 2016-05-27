<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

/**
 * Class Yireo_EmailTester_Model_Data_Generic
 */
class Yireo_EmailTester_Model_Data_Generic
{
    /**
     * @var Mage_Adminhtml_Model_Session
     */
    protected $session;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $request;
    
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;
    
    /**
     * @var Mage_Core_Model_App
     */
    protected $app;
    
    /**
     * @var Yireo_EmailTester_Helper_Output
     */
    protected $outputHelper;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->outputHelper = Mage::helper('emailtester/output');
        $this->session = Mage::getSingleton('admin/session');
        $this->request = Mage::app()->getRequest();
        $this->app = Mage::app();
        $this->store = Mage::getModel('core/store');
    }

    /**
     * Get an array of all options defined in the extension settings
     *
     * @param string $type
     *
     * @return array
     */
    protected function getCustomOptions($type = null)
    {
        $customOptions = $this->getStoreConfig('emailtester/settings/custom_' . $type);
        if (empty($customOptions)) {
            return array();
        }

        $options = array();
        $customOptions = explode(',', $customOptions);
        foreach ($customOptions as $customOption) {
            $customOption = (int)trim($customOption);
            if ($customOption > 0) {
                $options[] = $customOption;
            }
        }

        return $options;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function isValidId($id)
    {
        if (empty($id)) {
            return false;
        }
        
        if (!is_numeric($id)) {
            return false;
        }
        
        if ($id < 1) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the current store
     *
     * @return int|mixed
     * @throws Exception
     */
    protected function getStoreId()
    {
        $storeId = (int)$this->request->getParam('store');
        if (!$storeId > 0) {
            $storeId = $this->session->getData('emailtester.store');
        }

        return $storeId;
    }

    /**
     * @return int
     */
    protected function getWebsiteId()
    {
        $storeId = $this->getStoreId();
        if ($storeId > 0) {
            $store = $this->store->load($storeId);
            return $store->getWebsiteId();
        }

        return 0;
    }

    /**
     * @param $value
     *
     * @return null|string
     */
    protected function getStoreConfig($value)
    {
        return $this->app->getStore()->getConfig($value);
    }
}