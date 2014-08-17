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
 * EmailTester Core model
 */
class Yireo_EmailTester_Model_Mailer extends Mage_Core_Model_Abstract
{    
    public function doPrint()
    {
        $this->prepare();

        $template = $this->getTemplate();
        $storeId = $this->getStoreId();
        $variables = $this->getVariables($storeId);
        
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        $mail = $this->getEmailTemplate();
        
        $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
        if(is_numeric($template)) {
            $mail->load($template);
        } else {
            $mail->loadDefault($template, $localeCode);
        }

        // @todo: Send some extra headers
        //@header('Content-Type: text/html; charset=UTF-8');

        $text = $mail->getProcessedTemplate($variables, true);
        echo $text;
        exit;
    }

    public function send()
    {
        $this->prepare();
        
        $template = $this->getTemplate();
        $storeId = $this->getStoreId();
        $variables = $this->getVariables($storeId);
        
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        $mail = $this->getEmailTemplate();
        
        if(empty($senderName)) $senderName = Mage::getStoreConfig('trans_email/ident_general/name');
        if(empty($senderEmail)) $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $sender = array('name' => $senderName, 'email' => $senderEmail);

        $recipientEmail = $this->getEmail();
        if(empty($recipientEmail)) {
            $recipientEmail = $senderEmail;
        }
        
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        $recipientName = $customer->getName();
        if(empty($recipientName)) {
            $recipientName = $senderName;
        }


        $rt = false;
        $sent = false;
        $errors = array();

        try {
            $rt = $mail->sendTransactional($template, $sender, $recipientEmail, $recipientName, $variables);
            $sent = $mail->getSentSuccess();
            $translate->setTranslateInline(true);

        } catch(Exception $e) {
            $errors[] = $e->getMessage();
        }

        if($sent == false) {
            if(Mage::getStoreConfigFlag('system/smtp/disable')) $errors[] = 'SMTP is disabled';
            if($mail->getSenderName() == false) $errors[] = 'Sender name is missing';
            if($mail->getSenderEmail() == false) $errors[] = 'Sender email is missing';
            if($mail->getTemplateSubject() == false) $errors[] = 'Template subject is missing';
        }

        if($rt == false || $sent == false) {
            if(empty($errors)) $errors[] = 'Check your Magento logs';
            $this->setError(implode('; ', $errors));
            return false;
        }
        
        return true;
    }

    public function getEmailTemplate()
    {
        $storeId = $this->getStoreId();
        $mail = Mage::getModel('core/email_template');
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        return $mail;
    }

    public function prepare()
    {
        $storeId = $this->getStoreId();
        if(empty($storeId)) {
            $this->setStoreId(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());
        }
    }
    
    public function getVariables($storeId)
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        $store = Mage::getModel('core/store')->load($storeId);
        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        $order = Mage::getModel('sales/order')->load($this->getOrderId());
        if($order->getCustomerId() > 0) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        } else {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        }
        $customer->setPassword('fakepassword');
        
        // Try to load the payment block
        try {
            $paymentBlockHtml = $this->getPaymentBlockHtml($order, $storeId);
        } catch(Exception $e) {
            $paymentBlockHtml = 'No payment-data available';
        }
        
        // Try to load the invoice
        try {
            $invoice = $order->getInvoiceCollection()->getFirstItem();
        } catch(Exception $e) {
            $invoice = Mage::getModel('sales/order_invoice');
        }

        // Try to load the shipment
        try {
            $shipment = $order->getShipmentsCollection()->getFirstItem();
        } catch(Exception $e) {
            $shipment = Mage::getModel('sales/order_shipment');
        }

        // Try to load the creditmemos
        $creditmemos = $order->getCreditmemosCollection();
        if($creditmemos->getSize() > 0) {
            $creditmemo = $creditmemos->getFirstItem();
        } else {
            $creditmemo = null;
        }

        $variables = array(
            'store' => $store,
            'customer' => $customer,
            'product' => $product,
            'order' => $order,
            'shipment' => $shipment,
            'invoice' => $invoice,
            'creditmemo' => $creditmemo,
            'billing' => $customer->getPrimaryBillingAddress(),
            'comment' => 'This is a sample comment inserted by Yireo_EmailTester.',
            'payment_html' => $paymentBlockHtml,
        );

        // Allow for other extensions to add their own variables as well
        Mage::dispatchEvent('emailtester_variables', array('variables' => &$variables));

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        
        return $variables;
    }
    
    public function getPaymentBlockHtml($order, $storeId)
    {
        try {
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            return $paymentBlock->toHtml();
        } catch (Exception $exception) {
            return null;
        }
    }
}
