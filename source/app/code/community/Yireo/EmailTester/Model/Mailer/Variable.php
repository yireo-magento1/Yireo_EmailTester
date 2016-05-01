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
 * EmailTester model
 */
class Yireo_EmailTester_Model_Mailer_Variable extends Varien_Object
{
    /**
     * @var array
     */
    protected $variableNames = array(
        'store',
        'order',
        'customer',
        'product',
        'quote',
        'shipment',
        'invoice',
        'creditmemo',
        'billing',
        'comment',
        'payment_html',
    );

    /**
     * Return all variables from underlying models
     * 
     * @return array
     */
    public function getVariables()
    {
        $variables = array();
        $variables['template'] = $this->getTemplate();
        
        foreach ($this->variableNames as $variableName) {
            $variableModel = Mage::getModel('emailtester/mailer_variable_' . $variableName);
            $variableModel->setData($this->getData());
            $variableValue = $variableModel->getVariable();

            $this->setData($variableName, $variableValue);
            $variables[$variableName] = $variableValue;
        }
        
        return $variables;
    }
}