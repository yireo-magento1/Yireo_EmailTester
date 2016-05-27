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
 * Class Yireo_EmailTester_Model_Data_Order
 */
class Yireo_EmailTester_Model_Data_Order extends Yireo_EmailTester_Model_Data_Generic
{
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $orderModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->orderModel = Mage::getModel('sales/order');
    }

    /**
     * @param int $orderId
     * 
     * @return Mage_Core_Model_Abstract
     */
    public function getOrder($orderId)
    {
        return $this->orderModel->load($orderId);
    }

    /**
     * Get the current order ID
     *
     * @return int
     */
    public function getOrderId()
    {
        $orderId = $this->request->getParam('order_id');
        if (!empty($orderId)) {
            return $orderId;
        }

        $userData = $this->session->getData();
        $orderId = (isset($userData['emailtester.order_id'])) ? (int)$userData['emailtester.order_id'] : null;

        if (!empty($orderId)) {
            return $orderId;
        }

        $orderId = $this->getStoreConfig('emailtester/settings/default_order');
        return $orderId;
    }
    
    /**
     * Get an array of order select options
     *
     * @return array
     */
    public function getOrderOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $currentValue = $this->getOrderId();
        $orders = $this->getOrderCollection();

        foreach ($orders as $order) {
            /** @var Mage_Sales_Model_Order $order */
            $value = $order->getId();
            $label = '[' . $order->getId() . '] ' . $this->outputHelper->getOrderOutput($order);
            $current = ($order->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function getOrderCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $orders */
        $orders = $this->orderModel->getCollection()
            ->setOrder('increment_id', 'DESC');

        $limit = $this->getStoreConfig('emailtester/settings/limit_order');
        if ($limit > 0) {
            $orders->setPage(0, $limit);
        }

        $storeId = $this->getStoreId();
        if ($storeId > 0) {
            $store = $this->store->load($storeId);
            $website = $store->getWebsite();
            $storeIds = array();
            foreach ($website->getStores() as $store) {
                /** @var Mage_Core_Model_Store $store */
                $storeIds[] = $store->getId();
            }
            $orders->addFieldToFilter('store_id', $storeIds);
        }

        $customOptions = $this->getCustomOptions('order');
        if (!empty($customOptions)) {
            $orders->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        return $orders;
    }

    /**
     * Get current order result
     *
     * @return string
     */
    public function getOrderSearch()
    {
        $orderId = $this->getOrderId();

        if ($this->isValidId($orderId)) {
            /** @var Mage_Sales_Model_Order $order */
            $order = $this->orderModel->load($orderId);
            return $this->outputHelper->getOrderOutput($order);
        }

        return '';
    }
}