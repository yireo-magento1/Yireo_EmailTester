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
class Yireo_EmailTester_Model_Mailer_Variable_Customer extends Varien_Object
{
    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getVariable()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder();

        if ($order->getCustomerId() > 0 && $this->getCustomerId() == 0) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        } else {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        }

        // Load the first customer instead
        if (!$customer->getId() > 0) {
            $customer = Mage::getModel('customer/customer')->getCollection()->getFirstItem();
        }

        // Complete other customer fields
        $customer->setPassword('p@$$w0rd');

        return $customer;
    }

}