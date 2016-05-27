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
 * Class Yireo_EmailTester_Model_Data_Customer
 */
class Yireo_EmailTester_Model_Data_Customer extends Yireo_EmailTester_Model_Data_Generic
{
    /** @var Mage_Customer_Model_Customer */
    protected $customerModel;

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->customerModel = Mage::getModel('customer/customer');
    }

    /**
     * @param int $customerId
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getCustomer($customerId)
    {
        return $this->customerModel->load($customerId);
    }

    /**
     * Get the current customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        $customerId = $this->request->getParam('customer_id');
        if (!empty($customerId)) {
            return $customerId;
        }

        $userData = $this->session->getData();
        $customerId = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if (!empty($customerId)) {
            return $customerId;
        }

        $customerId = $this->getStoreConfig('emailtester/settings/default_customer');
        return $customerId;
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
        $customers = $this->getCustomerCollection();

        foreach ($customers as $customer) {
            /** @var Mage_Customer_Model_Customer $customer */
            $value = $customer->getId();
            $label = '[' . $customer->getId() . '] ' . $this->outputHelper->getCustomerOutput($customer);
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

        if ($this->isValidId($customerId)) {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $this->customerModel->load($customerId);
            return $this->outputHelper->getCustomerOutput($customer);
        }

        return '';
    }

    /**
     * @return Mage_Customer_Model_Resource_Customer_Collection
     * @throws Mage_Core_Exception
     */
    protected function getCustomerCollection()
    {
        /** @var Mage_Customer_Model_Resource_Customer_Collection $customers */
        $customers = $this->customerModel->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC');

        $limit = $this->getCustomerCollectionLimit();
        if ($limit > 0) {
            $customers->setPage(0, $limit);
        }

        $websiteId = $this->getWebsiteId();
        if ($websiteId > 0) {
            $customers->addAttributeToFilter('website_id', $websiteId);
        }

        $customOptions = $this->getCustomOptions('customer');
        if (!empty($customOptions)) {
            $customers->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        return $customers;
    }

    /**
     * @return null|string
     */
    protected function getCustomerCollectionLimit()
    {
        return $this->getStoreConfig('emailtester/settings/limit_customer');
    }
}