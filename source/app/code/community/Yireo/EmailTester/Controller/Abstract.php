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
 * EmailTester abstract controller
 *
 * @category    EmailTester
 * @package     Yireo_EmailTester
 */
class Yireo_EmailTester_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * Output the email in the browser
     *
     * @return void
     */
    protected function outputMail()
    {
        /* @var Yireo_EmailTester_Model_Mailer $mailer */
        $mailer = Mage::getModel('emailtester/mailer');
        $mailer->setTemplate($this->template);
        $mailer->setEmail($this->email);
        $mailer->setStoreId($this->storeId);
        $mailer->setOrderId($this->orderId);
        $mailer->setProductId($this->productId);
        $mailer->setCustomerId($this->customerId);

        // Print the mail
        $mailer->doPrint();
    }

    /**
     * Send the email to a recipient
     *
     * @return bool
     */
    protected function sendMail()
    {
        /* @var Yireo_EmailTester_Model_Mailer $mailer */
        $mailer = Mage::getModel('emailtester/mailer');
        $mailer->setTemplate($this->template);
        $mailer->setEmail($this->email);
        $mailer->setStoreId($this->storeId);
        $mailer->setOrderId($this->orderId);
        $mailer->setProductId($this->productId);
        $mailer->setCustomerId($this->customerId);

        // Send the mail
        if ($mailer->send() == true) {
            Mage::getModel('adminhtml/session')->addSuccess($this->__('Mail sent to %s', $mailer->getEmail()));
            return true;
        } else {
            Mage::getModel('adminhtml/session')->addError($this->__('Error sending mail: %s', $mailer->getError()));
            return false;
        }
    }

    /**
     * Get a certain POST value and store it in the session automatically
     *
     * @param $name
     *
     * @return mixed
     */
    protected function getPostValue($name)
    {
        $value = $this->getRequest()->getParam($name);
        if (empty($value)) {
            $value = Mage::getSingleton('adminhtml/session')->getData('emailtester.' . $name);
        }

        if (!empty($value)) {
            Mage::getSingleton('adminhtml/session')->setData('emailtester.' . $name, $value);
        }
        return $value;
    }

    /**
     * Prepend a page-title
     *
     * @param $subtitles array
     */
    protected function prependTitle($subtitles)
    {
        $headBlock = $this->getLayout()->getBlock('head');
        $title = $headBlock->getTitle();
        if (!is_array($subtitles)) {
            $subtitles = array($subtitles);
        }

        $headBlock->setTitle(implode(' / ', $subtitles) . ' / ' . $title);
    }

    /**
     * Return a search for customer data
     *
     * @param $search
     *
     * @return array
     */
    protected function getCustomerData($search)
    {
        $limit = Mage::getStoreConfig('emailtester/settings/limit_customer');
        if ($limit > 100) {
            $limit = 100;
        }

        if ($limit < 10) {
            $limit = 10;
        }

        $customers = Mage::getModel('customer/customer')->getCollection();
        $customers
            ->addAttributeToSelect(array('email', 'firstname', 'lastname'))
            ->setCurPage(0)
            ->setPageSize($limit)
        ;

        $customers->addAttributeToFilter(array(
            array('attribute' => 'firstname', 'like' => '%' . $search . '%'),
            array('attribute' => 'lastname', 'like' => '%' . $search . '%'),
            array('attribute' => 'email', 'like' => '%' . $search . '%'),
        ));

        $storeId = Mage::getSingleton('adminhtml/session')->getData('emailtester.store');
        if ($storeId > 0) {
            $store = Mage::getModel('core/store')->load($storeId);
            $websiteId = $store->getWebsiteId();
            $customers->addAttributeToFilter('website_id', $websiteId);
        }

        $data = array();
        foreach ($customers as $customer) {
            $data[] = array(
                'id' => $customer->getId(),
                'value' => Mage::helper('emailtester')->getCustomerOutput($customer),
            );
        }

        return $data;
    }
    /**
     * Return a search for product data
     *
     * @param $search
     *
     * @return array
     */
    protected function getProductData($search)
    {
        $limit = Mage::getStoreConfig('emailtester/settings/limit_product');
        if ($limit > 100) {
            $limit = 100;
        }

        if ($limit < 10) {
            $limit = 10;
        }

        $products = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect(array('sku', 'name', 'short_description'))
            ->setCurPage(0)
            ->setPageSize($limit)
        ;

        $products->addAttributeToFilter(array(
            array('attribute' => 'name', 'like' => '%' . $search . '%'),
            array('attribute' => 'sku', 'like' => '%' . $search . '%'),
        ));

        $data = array();
        foreach ($products as $product) {
            $data[] = array(
                'id' => $product->getId(),
                'value' => Mage::helper('emailtester')->getProductOutput($product),
            );
        }

        return $data;
    }

    /**
     * Return a search for order data
     *
     * @param $search
     *
     * @return array
     */
    protected function getOrderData($search)
    {
        $limit = Mage::getStoreConfig('emailtester/settings/limit_order');
        if ($limit > 100) {
            $limit = 100;
        }

        if ($limit < 10) {
            $limit = 10;
        }

        $orders = Mage::getModel('sales/order')
            ->getCollection()
            ->setCurPage(0)
            ->setPageSize($limit)
            ->addFieldToSelect('*')
        ;

        $storeId = Mage::getSingleton('adminhtml/session')->getData('emailtester.store');
        if ($storeId > 0) {
            $orders->addFieldToFilter('store_id', $storeId);
        }

        $orders->addFieldToFilter(
            array('increment_id', 'customer_email', 'customer_firstname', 'customer_lastname'),
            array(
                array('like' => '%' . $search . '%'),
                array('like' => '%' . $search . '%'),
                array('like' => '%' . $search . '%'),
                array('like' => '%' . $search . '%'),
            )
        );

        $data = array();
        foreach ($orders as $order) {
            $data[] = array(
                'id' => $order->getId(),
                'value' => Mage::helper('emailtester')->getOrderOutput($order),
            );
        }

        return $data;
    }
}
