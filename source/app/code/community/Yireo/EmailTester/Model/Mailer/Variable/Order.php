<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * EmailTester Core model
 */
class Yireo_EmailTester_Model_Mailer_Variable_Order extends Varien_Object
{
    /**
     * @return Mage_Core_Model_Store
     */
    public function getVariable()
    {
        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        // Load the first order instead
        if (!$order->getId() > 0) {
            $order = Mage::getModel('sales/order')->getCollection()->getFirstItem();
        }

        // Set the customer into the order
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        if ($customer->getId() > 0) {
            $order->setCustomerId($customer->getId());
            $order->setCustomer($customer);
            foreach ($customer->getData() as $name => $value) {
                $order->setData('customer_' . $name, $value);
            }
        }
        
        return $order;
    }
}