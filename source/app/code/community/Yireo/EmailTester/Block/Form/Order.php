<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
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
    
    public function getCurrentOrder()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.order_id'])) ? (int)$userData['emailtester.order_id'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_order');
        }
        return $currentValue;
    }
    
    public function getOrderOptions()
    {
        $currentValue = $this->getCurrentOrder();
        $limit = Mage::getStoreConfig('emailtester/settings/limit_order');
        $orders = Mage::getModel('sales/order')->getCollection()
            ->setOrder('increment_id', 'DESC')
        ;

        if($limit > 0) $orders->setPage(0, $limit);

        $storeId = $this->getStoreId();
        if($storeId > 0) {
            $orders->addFieldToFilter('store_id', $storeId);
        }

        $customOptions = $this->getCustomOptions('order');
        if(!empty($customOptions)) {
            $orders->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        $options = array();
        foreach($orders as $order) {
            $value = $order->getId();
            $label = '['.$order->getId().'] '.$order->getIncrementId().' ('.$order->getState().')';
            $current = ($order->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }
        return $options;
    }
}
