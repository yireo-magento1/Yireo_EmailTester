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
class Yireo_EmailTester_Model_Mailer_Variable_Creditmemo extends Varien_Object
{
    /**
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getVariable()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder();

        /** @var Varien_Data_Collection_Db $creditmemos */
        $creditmemos = $order->getCreditmemosCollection();
        if (!empty($creditmemos) && $creditmemos->getSize() > 0) {
            $creditmemo = $creditmemos->getFirstItem();
        } else {
            $creditmemo = null;
        }

        return $creditmemo;
    }
}