<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_EmailTester_Block_Form_Customer extends Yireo_EmailTester_Block_Form_Abstract
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('emailtester/form/customer.phtml');

        $customerId = $this->getRequest()->getParam('customer_id', 0);
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $this->setData('customer', $customer);
    }

    /**
     * Get the current customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_customer');
        }
        return $currentValue;
    }

    /**
     * Get an array of customer select options
     *
     * @return array
     */
    public function getCustomerOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $currentValue = $this->getCustomerId();
        $limit = Mage::getStoreConfig('emailtester/settings/limit_customer');

        /* @var Mage_Customer_Model_Resource_Customer_Collection $customers */
        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC')
        ;

        if($limit > 0) {
            $customers->setPage(0, $limit);
        }

        $storeId = $this->getStoreId();
        if($storeId > 0) {
            $store = Mage::getModel('core/store')->load($storeId);
            $websiteId = $store->getWebsiteId();
            $customers->addAttributeToFilter('website_id', $websiteId);
        }

        $customOptions = $this->getCustomOptions('customer');
        if(!empty($customOptions)) {
            $customers->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        foreach($customers as $customer) {
            /* @var Mage_Customer_Model_Customer $customer */
            $value = $customer->getId();
            $label = '['.$customer->getId().'] '.$this->helper->getCustomerOutput($customer);
            $current = ($customer->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    /**
     * Get current customer result
     *
     * @return string
     */
    public function getCustomerSearch()
    {
        $customerId = $this->getCustomerId();

        if(!empty($customerId)) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            return $this->helper->getCustomerOutput($customer);
        }
    }
}
