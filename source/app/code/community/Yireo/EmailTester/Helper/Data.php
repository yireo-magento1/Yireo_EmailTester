<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * EmailTester helper
 */
class Yireo_EmailTester_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Switch to determine whether this extension is enabled or not
     * 
     * @access public
     * @param null
     * @return string
     */
    public function enabled()
    {
        return true;
    }

    /*
     * Return the default email
     * 
     * @access public
     * @param null
     * @return string
     */
    public function getDefaultEmail()
    {
        return Mage::getStoreConfig('emailtester/settings/default_email');
    }

    public function getCustomerOutput($customer)
    {
        return $customer->getName() . ' ['.$customer->getEmail().']';
    }

    public function getProductOutput($product)
    {
        return $product->getName() . ' ['.$product->getSku().']';
    }

    public function getOrderOutput($order)
    {
        return $order->getIncrementId() . ' ['.$order->getCustomerEmail().']';
    }
}
