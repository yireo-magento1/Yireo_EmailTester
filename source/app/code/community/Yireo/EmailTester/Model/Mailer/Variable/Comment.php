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
class Yireo_EmailTester_Model_Mailer_Variable_Comment extends Varien_Object
{
    /**
     * @return string
     */
    public function getVariable()
    {
        return 'This is a sample comment inserted by Yireo_EmailTester.';
    }
}