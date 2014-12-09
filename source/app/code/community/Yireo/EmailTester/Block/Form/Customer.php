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
    
    public function getCustomerId()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_customer');
        }
        return $currentValue;
    }

    public function getCustomerSearch()
    {
        $customerId = $this->getCustomerId(); 
        if(!empty($customerId)) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            return Mage::helper('emailtester')->getCustomerOutput($customer);
        }
    }
}
