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
 * Class Yireo_EmailTester_Block_Print
 */
class Yireo_EmailTester_Block_Print extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/print.phtml');
    }

    /**
     * @param $bodyId
     */
    public function setBody($bodyId)
    {
        $this->setData('body_id', $bodyId);
    }
}
