<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

/**
 * EmailTester abstract controller
 *
 * @category    EmailTester
 * @package     Yireo_EmailTester
 */
class Yireo_EmailTester_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    protected function outputMail($template, $email, $storeId, $customerId, $productId, $orderId)
    {             
        // Load the mail
        $mailer = Mage::getModel('emailtester/mailer');
        $mailer->setTemplate($template);
        $mailer->setEmail($email);
        $mailer->setStoreId($storeId);
        $mailer->setOrderId($orderId);
        $mailer->setProductId($productId);
        $mailer->setCustomerId($customerId);
        
        // Print the mail
        $mailer->doPrint();
    }

    protected function sendMail($template, $email, $storeId, $customerId, $productId, $orderId)
    {             
        // Load the mail
        $mailer = Mage::getModel('emailtester/mailer');
        $mailer->setTemplate($template);
        $mailer->setEmail($email);
        $mailer->setStoreId($storeId);
        $mailer->setOrderId($orderId);
        $mailer->setProductId($productId);
        $mailer->setCustomerId($customerId);
        
        // Send the mail
        if($mailer->send() == true) {
            Mage::getModel('adminhtml/session')->addSuccess($this->__('Mail sent to %s', $mailer->getEmail()));
            return true;
        } else {
            Mage::getModel('adminhtml/session')->addError($this->__('Error sending mail: %s', $mailer->getError()));
            return false;
        }
    }

    protected function getPostValue($name)
    {
        $value = $this->getRequest()->getParam($name);
        if(!empty($value)) {
            Mage::getSingleton('adminhtml/session')->setData('emailtester.'.$name, $value);
        }
        return $value;
    }

    /*
     * Method to prepend a page-title
     *
     * @access public
     * @param $subtitles array
     * @return null
     */
    protected function prependTitle($subtitles)
    {
        $headBlock = $this->getLayout()->getBlock('head');
        $title = $headBlock->getTitle();
        if(!is_array($subtitles)) $subtitles = array($subtitles);
        $headBlock->setTitle(implode(' / ', $subtitles).' / '.$title);
    }

    protected function getCustomerData($search)
    {
        $limit = Mage::getStoreConfig('emailtester/settings/limit_customer');
        if($limit > 100) $limit = 100;
        if($limit < 10) $limit = 10;

        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect(array('email', 'firstname', 'lastname'))
            ->setCurPage(0)
            ->setPageSize($limit)
        ;

        $customers->addAttributeToFilter(array(
            array('attribute' => 'firstname', 'like' => '%'.$search.'%'),
            array('attribute' => 'lastname', 'like' => '%'.$search.'%'),
            array('attribute' => 'email', 'like' => '%'.$search.'%'),
        ));

        /*
        $storeId = $this->getStoreId();
        if($storeId > 0) {
            $store = Mage::getModel('core/store')->load($storeId);
            $websiteId = $store->getWebsiteId();
            $customers->addAttributeToFilter('website_id', $websiteId);
        }
        */

        $data = array();
        foreach($customers as $customer) {
            $customer = $customer->load($customer->getId());
            $data[] = array(
                'id' => $customer->getId(),
                'value' => Mage::helper('emailtester')->getCustomerOutput($customer),
            );
        }

        return $data;
    }

    protected function getProductData($search)
    {
        $limit = Mage::getStoreConfig('emailtester/settings/limit_product');
        if($limit > 100) $limit = 100;
        if($limit < 10) $limit = 10;

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('sku', 'name', 'short_description'))
            ->setCurPage(0)
            ->setPageSize($limit)
        ;

        $products->addAttributeToFilter(array(
            array('attribute' => 'name', 'like' => '%'.$search.'%'),
            array('attribute' => 'sku', 'like' => '%'.$search.'%'),
        ));

        $data = array();
        foreach($products as $product) {
            $product = $product->load($product->getId());
            $data[] = array(
                'id' => $product->getId(),
                'value' => Mage::helper('emailtester')->getProductOutput($product),
            );
        }

        return $data;
    }

    protected function getOrderData($search)
    {
        $limit = Mage::getStoreConfig('emailtester/settings/limit_order');
        if($limit > 100) $limit = 100;
        if($limit < 10) $limit = 10;

        $orders = Mage::getModel('sales/order')->getCollection()
            ->setCurPage(0)
            ->setPageSize($limit)
        ;

        $orders->addFieldToFilter('increment_id', array('like' => '%'.$search.'%'));

        $data = array();
        foreach($orders as $order) {
            $order = $order->load($order->getId());
            $data[] = array(
                'id' => $order->getId(),
                'value' => Mage::helper('emailtester')->getOrderOutput($order),
            );
        }

        return $data;
    }
}
