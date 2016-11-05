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
 * EmailTester Core model
 */
class Yireo_EmailTester_Model_Mailer extends Mage_Core_Model_Abstract
{
    /**
     * Include the behaviour of handling errors
     */
    use Yireo_EmailTester_Trait_Errorable;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var string
     */
    protected $template;

    /**
     * Output the email
     */
    public function doPrint()
    {
        $this->prepare();

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = $this->getData('template');
        $storeId = $this->getStoreId();
        $variables = $this->collectVariables();

        $mailer = $this->getEmailTemplate();

        $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
        if (is_numeric($template)) {
            $mailer->load($template);
        } else {
            $mailer->loadDefault($template, $localeCode);
        }

        // Send some extra headers just make sure the document is compliant
        $this->sendHeaders();
        $body = $mailer->getProcessedTemplate($variables);
        $fixHeader = (bool)Mage::getStoreConfig('emailtester/settings/fix_header');

        if (strstr($body, '<html') == false && $fixHeader == true) {
            /** @var Yireo_EmailTester_Block_Print $block */
            $block = Mage::app()->getLayout()->createBlock('emailtester/print');
            echo $block->setBody($body)->toHtml();
        } else {
            echo $body;
        }

        exit;
    }

    /**
     * Send HTTP headers
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        header('Content-Type: text/html; charset=UTF-8');
    }

    /**
     * Send the email
     *
     * @return bool
     */
    public function send()
    {
        $this->prepare();

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $sender = Mage::getModel('emailtester/mailer_addressee');
        $recipient = $this->getRecipient();

        $variables = $this->collectVariables();
        $mailer = $this->getMailer();

        /** @var $emailInfo Mage_Core_Model_Email_Info */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($recipient->getEmail(), $recipient->getName());
        $mailer->addEmailInfo($emailInfo);

        try {
            $mailer->setSender($sender->getAsArray());

            // Set all required params and send emails
            $mailer->setStoreId($this->getStoreId());
            $mailer->setTemplateId($this->getData('template'));
            $mailer->setTemplateParams($variables);

            $sent = $mailer->send();

        } catch (Exception $e) {
            $sent = false;
            Mage::logException($e);
            $this->addError($e->getMessage());
        }

        $translate->setTranslateInline(true);

        if ($sent == false) {
            $this->processMailerErrors($mailer);
            return false;
        }

        return true;
    }

    /**
     * @param $mailer
     */
    protected function processMailerErrors($mailer)
    {
        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            $this->addError('SMTP is disabled');
        }

        $sender = Mage::getModel('emailtester/mailer_addressee');
        $senderArray = $sender->getAsArray();

        if (empty($senderArray['name'])) {
            $this->addError('Sender name is missing');
        }

        if (empty($senderArray['email'])) {
            $this->addError('Sender email is missing');
        }

        if (!$this->hasErrors()) {
            $this->addError('Check your logs for unknown error');
        }
    }

    /**
     * @return Yireo_EmailTester_Model_Mailer_Addressee
     */
    protected function getRecipient()
    {
        $recipient = Mage::getModel('emailtester/mailer_addressee');

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        if ($customer->getId() > 0) {
            $recipient->setName($customer->getName());
            $recipient->setEmail($customer->getEmail());
        }

        $recipientEmail = $this->getData('email');
        if (!empty($recipientEmail)) {
            $recipient->setEmail($recipientEmail);
        }

        return $recipient;
    }

    /**
     * Get the email template object
     *
     * @return Mage_Core_Model_Email_Template
     */
    protected function getEmailTemplate()
    {
        $storeId = $this->getStoreId();
        $mail = Mage::getModel('core/email_template');
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

        return $mail;
    }

    /**
     * Get the email template object
     *
     * @return Mage_Core_Model_Email_Template_Mailer
     */
    protected function getMailer()
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');

        return $mailer;
    }

    /**
     * Prepare for the main action
     *
     * @throws Mage_Core_Exception
     */
    protected function prepare()
    {
        $this->setDefaultStoreId();
    }

    /**
     * @throws Mage_Core_Exception
     */
    protected function setDefaultStoreId()
    {
        $storeId = $this->getStoreId();
        if (empty($storeId)) {
            $this->setStoreId(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());
        }
    }

    /**
     * Collect all variables to insert into the email template
     *
     * @return array
     */
    protected function collectVariables()
    {
        $storeId = $this->getStoreId();
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        $variableModel = Mage::getModel('emailtester/mailer_variable');
        $variableModel->setData($this->getData());
        $variables = $variableModel->getVariables();

        // Allow for other extensions to add their own variables as well
        $result = new Varien_Object($variables);
        Mage::dispatchEvent('emailtester_variables', array('variables' => &$variables, 'result' => $result));
        $variables = $result->getData();

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $variables;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @param int $storeId
     */
    protected function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getData('template');
    }

    /**
     * @param string $template
     */
    protected function setTemplate($template)
    {
        $this->setData('template', $template);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData('customerId');
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->setData('customerId', $customerId);
    }
}
