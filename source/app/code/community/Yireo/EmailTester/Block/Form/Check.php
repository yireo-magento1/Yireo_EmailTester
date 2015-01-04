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

class Yireo_EmailTester_Block_Form_Check extends Mage_Adminhtml_Block_Widget_Container
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('emailtester/form/checks.phtml');
    }
    
    public function getWarnings()
    {
        $warnings = array();

        return $warnings;
    }
}
