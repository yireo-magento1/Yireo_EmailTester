<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

// Automatically delete the old controller-file
$oldFile = dirname(__FILE__) . '/IndexController.php';
if (is_file($oldFile)) {
    @unlink($oldFile);
}

/**
 * EmailTester admin controller
 *
 * @category    EmailTester
 * @package     Yireo_EmailTester
 */
class Yireo_EmailTester_EmailtesterController extends Yireo_EmailTester_Controller_Abstract
{
    /**
     * Common method
     *
     * @access protected
     * @param null
     * @return Yireo_EmailTester_EmailtesterController
     */
    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('system/tools/emailtester')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
             ->_addBreadcrumb(Mage::helper('emailtester')->__('Email Tester'), Mage::helper('emailtester')->__('Email Tester'))
        ;
        $this->prependTitle(array('EmailTester', 'System', 'Tools'));
        return $this;
    }

    /**
     * Overview page
     *
     * @access public
     * @param null
     * @return null
     */
    public function indexAction()
    {
        // Call upon the values to save them into the session
        $template = $this->getPostValue('template');
        $email = $this->getPostValue('email');
        $storeId = (int) $this->getPostValue('store');
        $customerId = (int) $this->getPostValue('customer_id');
        $productId = (int) $this->getPostValue('product_id');
        $orderId = (int) $this->getPostValue('order_id');

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('emailtester/form'))
             ->renderLayout();
    }

    public function ajaxAction()
    {
        $term = $this->getRequest()->getParam('term');
        $type = $this->getRequest()->getParam('type');

        $data = array();
        if ($type == 'customer') {
            $data = $this->getCustomerData($term);
        } elseif ($type == 'order') {
            $data = $this->getOrderData($term);
        } else {
            $data = $this->getProductData($term);
        }

        echo json_encode($data);
        exit;
    }

    /**
     * Output an mail
     *
     * @access public
     * @param null
     * @return null
     */
    public function outputAction()
    {
        $template = $this->getPostValue('template');
        $email = $this->getPostValue('email');
        $storeId = (int) $this->getPostValue('store');
        $customerId = (int) $this->getPostValue('customer_id');
        $productId = (int) $this->getPostValue('product_id');
        $orderId = (int) $this->getPostValue('order_id');

        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
        }

        if (empty($template)) {
            Mage::getModel('adminhtml/session')->addError($this->__('No transactional email specified'));
            return $this->_redirect('adminhtml/emailtester/index');
        }

        if ($storeId < 1) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a specific Store View'));
            return $this->_redirect('adminhtml/emailtester/index');
        }

        if (empty($orderId)) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a sales order'));
            return $this->_redirect('adminhtml/emailtester/index');
        }

        // Output the mail
        $this->outputMail($template, $email, $storeId, $customerId, $productId, $orderId);
    }

    /**
     * Send an mail
     *
     * @access public
     * @param null
     * @return null
     */
    public function mailAction()
    {
        $template = $this->getPostValue('template');
        $email = $this->getPostValue('email');
        $storeId = (int) $this->getPostValue('store');
        $customerId = (int) $this->getPostValue('customer_id');
        $productId = (int) $this->getPostValue('product_id');
        $orderId = (int) $this->getPostValue('order_id');

        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
        }

        if (empty($template)) {
            Mage::getModel('adminhtml/session')->addError($this->__('No transactional email specified'));
            return $this->_redirect('adminhtml/emailtester/index');
        }

        if ($storeId < 1) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a specific Store View'));
            return $this->_redirect('adminhtml/emailtester/index');
        }

        if (empty($orderId)) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a sales order'));
            return $this->_redirect('adminhtml/emailtester/index');
        }

        // Send the mail
        $this->sendMail($template, $email, $storeId, $customerId, $productId, $orderId);

        // Redirect
        $this->_redirect('adminhtml/emailtester/index');
    }
}
