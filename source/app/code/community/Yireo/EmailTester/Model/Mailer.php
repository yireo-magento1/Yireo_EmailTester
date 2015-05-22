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
 * EmailTester Core model
 */
class Yireo_EmailTester_Model_Mailer extends Mage_Core_Model_Abstract
{
    /**
     * Output the email
     */
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

        // Send some extra headers just make sure the document is compliant
        if (headers_sent() == false) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        $body = $mail->getProcessedTemplate($variables, true);
        $fixHeader = (bool)Mage::getStoreConfig('emailtester/settings/fix_header');

        if(strstr($body, '<html') == false && $fixHeader == true) {
            echo Mage::app()->getLayout()->createBlock('emailtester/print')->setBody($body)->toHtml();
        } else {
            echo $body;
        }

        exit;
    }

    /**
     * Send the email
     *
     * @return bool
     */
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

    /**
     * Get the email template object
     *
     * @return Mage_Core_Model_Email_Template
     */
    public function getEmailTemplate()
    {
        $storeId = $this->getStoreId();
        $mail = Mage::getModel('core/email_template');
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

        return $mail;
    }

    /**
     * Prepare for the main action
     *
     * @throws Mage_Core_Exception
     */
    public function prepare()
    {
        $storeId = $this->getStoreId();
        if(empty($storeId)) {
            $this->setStoreId(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());
        }
    }

    /**
     * Collect all variables to insert into the email template
     *
     * @param $storeId
     *
     * @return array
     */
    public function getVariables($storeId)
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        $store = Mage::getModel('core/store')->load($storeId);
        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        $order = Mage::getModel('sales/order')->load($this->getOrderId());

        // Load the first product instead
        if(!$product->getId() > 0) {
            $product = Mage::getModel('catalog/product')->getCollection()->getFirstItem();
        }

        // Load the first order instead
        if(!$order->getId() > 0) {
            $order = Mage::getModel('sales/order')->getCollection()->getFirstItem();
        }

        if($order->getCustomerId() > 0 && $this->getCustomerId() == 0) {
            $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        } else {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        }

        // Load the first customer instead
        if(!$customer->getId() > 0) {
            $customer = Mage::getModel('customer/customer')->getCollection()->getFirstItem();
        }

        // Complete other customer fields
        $customer->setPassword('p@$$w0rd');

        // Set the customer into the order
        if($customer->getId() > 0) {
            $order->setCustomerId($customer->getId());
            $order->setCustomer($customer);
            foreach($customer->getData() as $name => $value) {
                $order->setData('customer_'.$name, $value);
            }
        }
        
        // Try to load the payment block
        try {
            $paymentBlockHtml = $this->getPaymentBlockHtml($order, $storeId);
        } catch(Exception $e) {
            $paymentBlockHtml = 'No payment-data available';
        }
        
        // Try to load the invoice
        try {
            $invoices = $order->getInvoiceCollection();
            if($invoices) {
                $invoice = $invoices->getFirstItem();
            } else {
                $invoice = Mage::getModel('sales/order_invoice');
            }
        } catch(Exception $e) {
            $invoice = Mage::getModel('sales/order_invoice');
        }

        // Try to load the shipment
        try {
            $shipments = $order->getShipmentsCollection();
            if($shipments) {
                $shipment = $shipments->getFirstItem();
            } else {
                $shipment = Mage::getModel('sales/order_shipment');
            }
        } catch(Exception $e) {
            $shipment = Mage::getModel('sales/order_shipment');
        }

        // Try to load the creditmemos
        $creditmemos = $order->getCreditmemosCollection();
        if(!empty($creditmemos) && $creditmemos->getSize() > 0) {
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

    /**
     *
     *
     * @param $order
     * @param $storeId
     *
     * @return null
     */
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
