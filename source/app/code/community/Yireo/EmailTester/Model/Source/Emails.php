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
 * Class Yireo_EmailTester_Model_Source_Emails
 */
class Yireo_EmailTester_Model_Source_Emails
{
    /**
     * Return a list of email templates
     *
     * @return array
     */
    public function toOptionArray()
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

        array_unshift($options, array('value' => '', 'label' => ''));

        return $options;
    }
}
