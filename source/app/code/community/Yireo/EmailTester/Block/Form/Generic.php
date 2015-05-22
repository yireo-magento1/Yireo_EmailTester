<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 * @contributor Philipp Wiegel
 */

class Yireo_EmailTester_Block_Form_Generic extends Mage_Adminhtml_Block_Widget_Container
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/form/generic.phtml');
    }

    /**
     * Get all email template options
     *
     * @return array
     */
    public function getTemplateOptions()
    {
        $options = array();

        $collection =  Mage::getResourceSingleton('core/email_template_collection')
            ->setOrder('template_code')
        ;

        if(!empty($collection)) {
            foreach($collection as $template) {
                $templateCode = $template->getTemplateCode();
                if(empty($templateCode)) $templateCode = $template->getData('orig_template_code');
                $options[$templateCode]['value'] = $template->getTemplateId();
                $options[$templateCode]['label'] = $templateCode;
            }
            ksort($options);
        }

        $defaultOptions = Mage::getModel('core/email_template')->getDefaultTemplatesAsOptionsArray();
        foreach($defaultOptions as $option) {
            if(empty($option['value'])) continue;
            if(!empty($collection)) {
                $option['label'] = '[default] '.$option['label'];
            }
            $options[] = $option;
        }

        return $options;
    }

    /**
     * Get the currently selected email template
     *
     * @return string
     */
    public function getCurrentTemplate()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.template'])) ? (int)$userData['emailtester.template'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_transactional');
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
        $email = Mage::getSingleton('adminhtml/session')->getData('emailtester.email');
        if(empty($email)) {
            $email = Mage::helper('emailtester')->getDefaultEmail();
        }
        return $email;
    }
}
