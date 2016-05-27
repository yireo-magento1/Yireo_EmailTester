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
 * Class Yireo_EmailTester_Model_Source_Emails
 */
class Yireo_EmailTester_Model_Source_Emails
{
    /**
     * @var Yireo_EmailTester_Model_Data_Template
     */
    protected $templateData;

    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->templateData = Mage::getModel('emailtester/data_template');
    }

    /**
     * Return a list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->templateData->getTemplateOptions();
        array_unshift($options, array('value' => '', 'label' => ''));

        return $options;
    }
}
