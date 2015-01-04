<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_EmailTester_Block_Form_Order extends Yireo_EmailTester_Block_Form_Abstract
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('emailtester/form/order.phtml');

        $orderId = $this->getRequest()->getParam('order_id', 0);
        $this->setOrder(Mage::getModel('sales/order')->load($orderId));
    }
    
    public function getOrderId()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.order_id'])) ? (int)$userData['emailtester.order_id'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_order');
        }
        return $currentValue;
    }

    public function getOrderSearch()
    {
        $orderId = $this->getOrderId(); 
        if(!empty($orderId)) {
            $order = Mage::getModel('sales/order')->load($orderId);
            return Mage::helper('emailtester')->getOrderOutput($order);
        }
    }
}
