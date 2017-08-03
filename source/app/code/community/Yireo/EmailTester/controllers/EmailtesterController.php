<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
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
            ->_addBreadcrumb($this->adminhtmlHelper->__('System'), $this->adminhtmlHelper->__('System'))
            ->_addBreadcrumb($this->adminhtmlHelper->__('Tools'), $this->adminhtmlHelper->__('Tools'))
            ->_addBreadcrumb($this->__('Email Tester'), $this->__('Email Tester'));
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

        $jsonData = Mage::helper('core')->jsonEncode($data);

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
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
        $this->setData('template', $this->getPostValue('template'));
        $this->setData('email', $this->getPostValue('email'));
        $this->setData('storeId', (int)$this->getPostValue('store'));
        $this->setData('customerId', (int)$this->getPostValue('customer_id'));
        $this->setData('productId', (int)$this->getPostValue('product_id'));
        $this->setData('orderId', (int)$this->getPostValue('order_id'));

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
        if ($this->isDataEmpty('template')) {
            $this->getAdminhtmlSession()->addError($this->__('No transactional email specified'));
            return false;
        }

        if ($this->isDataLowerThanOne('storeId')) {
            $this->getAdminhtmlSession()->addError($this->__('You need to specify a specific Store View'));
            return false;
        }

        if ($this->isDataEmpty('orderId')) {
            $this->getAdminhtmlSession()->addError($this->__('You need to specify a sales order'));
            return false;
        }

        $mailer = $this->mailer;
        $mailer->setData('template', $this->getData('template'));
        $mailer->setData('email', $this->getData('email'));
        $mailer->setData('store_id', $this->getData('storeId'));
        $mailer->setData('order_id', $this->getData('orderId'));
        $mailer->setData('product_id', $this->getData('productId'));
        $mailer->setData('customer_id', $this->getData('customerId'));
        
        return true;
    }

    /**
     * Output the email in the browser
     *
     * @return void
     */
    protected function outputMail()
    {
        // Print the mail
        $this->mailer->doPrint();
    }

    /**
     * Send the email to a recipient
     *
     * @return bool
     */
    protected function sendMail()
    {
        // Send the mail
        if ($this->mailer->send() === false) {
            $error = $this->mailer->getErrorString();

            if (!empty($error)) {
                $this->getAdminhtmlSession()->addError($this->__('Error sending mail: %s', $error));
            } else {
                $this->getAdminhtmlSession()->addError($this->__('Unknown error sending mail'));
            }

            return false;
        }

        $this->getAdminhtmlSession()->addSuccess($this->__('Mail sent to %s', $this->mailer->getEmail()));
        return true;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        $aclResource = 'admin/system/tools/emailtester';

        if (is_array($aclResource)) {
            $aclResource = $aclResource[0];
        }

        return $this->getSession()->isAllowed($aclResource);
    }
}
