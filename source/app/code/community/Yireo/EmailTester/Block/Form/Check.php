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

class Yireo_EmailTester_Block_Form_Check extends Yireo_EmailTester_Block_Form_Abstract
{
    /**
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();
        
        $this->setTemplate('emailtester/form/checks.phtml');
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        $warnings = array();

        return $warnings;
    }
}
