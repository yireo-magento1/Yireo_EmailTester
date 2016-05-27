<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

/**
 * Class Yireo_EmailTester_Block_Form_Customer
 */
class Yireo_EmailTester_Block_Form_Customer extends Yireo_EmailTester_Block_Form_Abstract
{
    /**
     * @var Yireo_EmailTester_Model_Data_Customer
     */
    protected $customerData;
    
    /**
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/form/customer.phtml');

        $this->customerData = Mage::getModel('emailtester/data_customer');
    }

    /**
     * Get the current customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerData->getCustomerId();
    }

    /**
     * Get an array of customer select options
     *
     * @return array
     */
    public function getCustomerOptions()
    {
        return $this->customerData->getCustomerOptions();
    }

    /**
     * Get current customer result
     *
     * @return string
     */
    public function getCustomerSearch()
    {
        return $this->customerData->getCustomerSearch();
    }
}
