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
 * EmailTester abstract controller
 *
 * @category    EmailTester
 * @package     Yireo_EmailTester
 */
class Yireo_EmailTester_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * Include the behaviour of handling errors
     */
    use Yireo_EmailTester_Trait_Datacontainable;
    
    /**
     * @var Mage_Adminhtml_Helper_Data
     */
    protected $adminhtmlHelper;

    /**
     * @var Yireo_EmailTester_Helper_Data
     */
    protected $helper;

    /**
     * @var Yireo_EmailTester_Helper_Output
     */
    protected $outputHelper;

    /**
     * @var Yireo_EmailTester_Model_Mailer
     */
    protected $mailer;

    /**
     * Pre dispatch action
     */
    public function preDispatch()
    {
        $this->adminhtmlHelper = Mage::helper('adminhtml');
        $this->helper = Mage::helper('emailtester');
        $this->outputHelper = Mage::helper('emailtester/output');
        $this->mailer = Mage::getModel('emailtester/mailer');

        return parent::preDispatch();
    }

    /**
     * Note: Do not move this to preDispatch() or constructor, because it will break
     *
     * @return Mage_Admin_Model_Session
     */
    protected function getSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * Note: Do not move this to preDispatch() or constructor, because it will break
     *
     * @return Mage_Admin_Model_Session
     */
    protected function getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
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
        if (empty($name)) {
            $this->getAdminhtmlSession()->addError('Empty argument for getPostValue()');
            return null;
        }

        $value = $this->getRequest()->getParam($name);

        if (empty($value)) {
            $value = $this->getSession()->getData('emailtester.' . $name);
        }

        if (!empty($value)) {
            $this->getSession()->setData('emailtester.' . $name, $value);
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
     * @todo: Move to data models
     */
    protected function getCustomerData($search)
    {
        $limit = $this->helper->getStoreConfig('emailtester/settings/limit_customer');
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
            ->setPageSize($limit);

        $customers->addAttributeToFilter(array(
            array('attribute' => 'firstname', 'like' => '%' . $search . '%'),
            array('attribute' => 'lastname', 'like' => '%' . $search . '%'),
            array('attribute' => 'email', 'like' => '%' . $search . '%'),
        ));

        $storeId = $this->getSession()->getData('emailtester.store');
        if ($storeId > 0) {
            $store = Mage::getModel('core/store')->load($storeId);
            $websiteId = $store->getWebsiteId();
            $customers->addAttributeToFilter('website_id', $websiteId);
        }

        $data = array();
        foreach ($customers as $customer) {
            $data[] = array(
                'id' => $customer->getId(),
                'value' => $this->outputHelper->getCustomerOutput($customer),
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
     * @todo: Move to data models
     */
    protected function getProductData($search)
    {
        $limit = $this->helper->getStoreConfig('emailtester/settings/limit_product');
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
            ->setPageSize($limit);

        $products->addAttributeToFilter(array(
            array('attribute' => 'name', 'like' => '%' . $search . '%'),
            array('attribute' => 'sku', 'like' => '%' . $search . '%'),
        ));

        $data = array();
        foreach ($products as $product) {
            $data[] = array(
                'id' => $product->getId(),
                'value' => $this->outputHelper->getProductOutput($product),
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
     * @todo: Move to data models
     */
    protected function getOrderData($search)
    {
        $limit = $this->helper->getStoreConfig('emailtester/settings/limit_order');
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
            ->addFieldToSelect('*');

        $storeId = $this->getSession()->getData('emailtester.store');
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
                'value' => $this->outputHelper->getOrderOutput($order),
            );
        }

        return $data;
    }
}
