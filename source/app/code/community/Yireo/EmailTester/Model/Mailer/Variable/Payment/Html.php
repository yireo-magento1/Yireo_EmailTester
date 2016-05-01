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
class Yireo_EmailTester_Model_Mailer_Variable_Payment_Html extends Varien_Object
{
    /**
     * @return string
     */
    public function getVariable()
    {
        // Try to load the payment block
        try {
            $paymentBlockHtml = $this->getPaymentBlockHtml($this->getOrder(), $this->getStoreId());
        } catch (Exception $e) {
            $paymentBlockHtml = 'No payment-data available';
        }
        
        return $paymentBlockHtml;
    }

    /**
     * Get the payment HTML block
     *
     * @param $order Mage_Sales_Model_Order
     * @param $storeId int
     *
     * @return string
     */
    public function getPaymentBlockHtml($order, $storeId)
    {
        try {
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment());
            $paymentBlock->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            return $paymentBlock->toHtml();
        } catch (Exception $exception) {
            return '';
        }
    }
}