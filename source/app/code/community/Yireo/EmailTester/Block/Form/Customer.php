<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
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
        $this->setCustomer(Mage::getModel('customer/customer')->load($customerId));
    }
    
    public function getCurrentCustomer()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_customer');
        }
        return $currentValue;
    }
    
    public function getCustomerOptions()
    {
        $currentValue = $this->getCurrentCustomer();
        $limit = Mage::getStoreConfig('emailtester/settings/limit_customer');
        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC')
        ;

        if($limit > 0) $customers->setPage(0, $limit);

        $storeId = $this->getStoreId();
        if($storeId > 0) {
            $customers->addAttributeToFilter('store_id', $storeId);
        }

        $customOptions = $this->getCustomOptions('customer');
        if(!empty($customOptions)) {
            $customers->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        $options = array();
        foreach($customers as $customer) {
            $value = $customer->getId();
            $label = '['.$customer->getId().'] '.$customer->getFirstname().' '.$customer->getLastname().' ('.$customer->getEmail().')';
            $current = ($customer->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }
        return $options;
    }
}
