<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 * @contributor Philipp Wiegel
 */

/**
 * Class Yireo_EmailTester_Block_Form_Generic
 */
class Yireo_EmailTester_Block_Form_Generic extends Yireo_EmailTester_Block_Form_Abstract
{
    /**
     * @var Yireo_EmailTester_Model_Data_Template
     */
    protected $templateData;

    /**
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/form/generic.phtml');

        $this->templateData = Mage::getModel('emailtester/data_template');
    }

    /**
     * Get all email template options
     *
     * @return array
     */
    public function getTemplateOptions()
    {
        return $this->templateData->getTemplateOptions();
    }

    /**
     * Get the currently selected email template
     *
     * @return string
     */
    public function getCurrentTemplate()
    {
        $userData = $this->session->getData();
        $currentValue = (isset($userData['emailtester.template'])) ? (int)$userData['emailtester.template'] : null;
        if (empty($currentValue)) {
            $currentValue = $this->helper->getStoreConfig('emailtester/settings/default_transactional');
        }
        return $currentValue;
    }

    /**
     * Get the current emailaddress
     *
     * @return string
     */
    public function getCurrentEmail()
    {
        $email = $this->session->getData('emailtester.email');
        if (empty($email)) {
            $email = $this->helper->getDefaultEmail();
        }
        return $email;
    }
}
