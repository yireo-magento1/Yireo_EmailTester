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
    unlink($oldFile);
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
     */
    public function indexAction()
    {
        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('emailtester/form'))
             ->renderLayout();
    }

    /**
     * Output JSON response
     */
    public function ajaxAction()
    {
        $term = $this->getRequest()->getParam('term');
        $type = $this->getRequest()->getParam('type');

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
     */
    public function outputAction()
    {
        $this->_gatherData();

        if ($this->_preflightCheck() == false) {
            $this->_redirect('adminhtml/emailtester/index');
            return;
        }

        // Output the mail
        $this->outputMail();
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
        $this->_gatherData();

        if ($this->_preflightCheck() == false) {
            $this->_redirect('adminhtml/emailtester/index');
            return;
        }

        // Send the mail
        $this->sendMail();

        // Redirect
        $this->_redirect('adminhtml/emailtester/index');
    }

    /**
     * Gather data from the request
     */
    protected function _gatherData()
    {
        $this->template = $this->getPostValue('template');
        $this->email = $this->getPostValue('email');
        $this->storeId = (int) $this->getPostValue('store');
        $this->customerId = (int) $this->getPostValue('customer_id');
        $this->productId = (int) $this->getPostValue('product_id');
        $this->orderId = (int) $this->getPostValue('order_id');

        if (Mage::app()->isSingleStoreMode()) {
            $this->storeId = Mage::app()->getStore(true)->getId();
        }

    }

    /**
     * Pre-flight check when testing an email
     *
     * @return bool
     */
    protected function _preflightCheck()
    {
        if (empty($this->template)) {
            Mage::getModel('adminhtml/session')->addError($this->__('No transactional email specified'));
            return false;
        }

        if ($this->storeId < 1) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a specific Store View'));
            return false;
        }

        if (empty($this->orderId)) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a sales order'));
            return false;
        }

        return true;
    }
}
