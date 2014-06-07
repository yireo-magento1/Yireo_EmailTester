<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright (C) 2014 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

// Automatically delete the old controller-file
$oldFile = dirname(__FILE__).'/IndexController.php';
if(is_file($oldFile)) @unlink($oldFile);

/**
 * EmailTester admin controller
 *
 * @category    EmailTester
 * @package     Yireo_EmailTester
 */
class Yireo_EmailTester_EmailtesterController extends Mage_Adminhtml_Controller_Action
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
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('emailtester/form'))
            ->renderLayout();
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
        $storeId = (int)$this->getPostValue('store');
        $customerId = (int)$this->getPostValue('customer_id');
        $productId = (int)$this->getPostValue('product_id');
        $orderId = (int)$this->getPostValue('order_id');

        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
        }
        
        if(empty($template)) {
            Mage::getModel('adminhtml/session')->addError($this->__('No transactional email specified'));
            return $this->_redirect('adminhtml/emailtester/index');
        }
        
        if($storeId < 1) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a specific Store View'));
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
        $storeId = (int)$this->getPostValue('store');
        $customerId = (int)$this->getPostValue('customer_id');
        $productId = (int)$this->getPostValue('product_id');
        $orderId = (int)$this->getPostValue('order_id');
        
        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
        }
        
        if(empty($template)) {
            Mage::getModel('adminhtml/session')->addError($this->__('No transactional email specified'));
            return $this->_redirect('adminhtml/emailtester/index');
        }
        
        if($storeId < 1) {
            Mage::getModel('adminhtml/session')->addError($this->__('You need to specify a specific Store View'));
            return $this->_redirect('adminhtml/emailtester/index');
        }
        
        // Send the mail
        $this->sendMail($template, $email, $storeId, $customerId, $productId, $orderId);

        // Redirect
        $this->_redirect('adminhtml/emailtester/index');
    }
    
    private function outputMail($template, $email, $storeId, $customerId, $productId, $orderId)
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

    private function sendMail($template, $email, $storeId, $customerId, $productId, $orderId)
    {             
        // Load the mail
        $mailer = Mage::getModel('emailtester/mailer');
        $mailer->setTemplate($template);
        $mailer->setEmail($email);
        $mailer->setStoreId($storeId);
        $mailer->setOrderId($orderId);
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
}
