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
 * EmailTester helper
 */
class Yireo_EmailTester_Helper_Output extends Mage_Core_Helper_Abstract
{
    /**
     * Output a string describing a customer record
     *
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return string
     */
    public function getCustomerOutput(Mage_Customer_Model_Customer $customer)
    {
        return $customer->getName() . ' ['.$customer->getData('email').']';
    }

    /**
     * Output a string describing a product record
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function getProductOutput(Mage_Catalog_Model_Product $product)
    {
        return $product->getName() . ' ['.$product->getSku().']';
    }

    /**
     * Output a string describing a customer record
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     */
    public function getOrderOutput(Mage_Sales_Model_Order $order)
    {
        return '#'.$order->getIncrementId() . ' ['.$order->getCustomerEmail().' / '.$order->getState().']';
    }
}
