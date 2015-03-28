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
        $currentValue = (isset($userData['emailtester.order_id'])) ? (int) $userData['emailtester.order_id'] : null;
        if (empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_order');
        }
        return $currentValue;
    }

    public function getOrderOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $limit = Mage::getStoreConfig('emailtester/settings/limit_order');
        $currentValue = $this->getOrderId();

        $orders = Mage::getModel('sales/order')->getCollection()
            ->setOrder('increment_id', 'DESC')
        ;

        if($limit > 0) $orders->setPage(0, $limit);

        $storeId = $this->getStoreId();
        if($storeId > 0) {
            $store = Mage::getModel('core/store')->load($storeId);
            $website = $store->getWebsite();
            $storeIds = array();
            foreach($website->getStores() as $store) {
                $storeIds[] = $store->getId();
            }
            $orders->addFieldToFilter('store_id', $storeIds);
        }

        $customOptions = $this->getCustomOptions('order');
        if(!empty($customOptions)) {
            $orders->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        foreach($orders as $order) {
            $value = $order->getId();
            $label = '['.$order->getId().'] '.$order->getIncrementId().' ('.$order->getState().')';
            $current = ($order->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    public function getOrderSearch()
    {
        $orderId = $this->getOrderId();
        if (!empty($orderId)) {
            $order = Mage::getModel('sales/order')->load($orderId);
            return Mage::helper('emailtester')->getOrderOutput($order);
        }
    }
}
