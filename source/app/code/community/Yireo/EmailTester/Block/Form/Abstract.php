<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

/**
 * Class Yireo_EmailTester_Block_Form_Abstract
 */
class Yireo_EmailTester_Block_Form_Abstract extends Mage_Adminhtml_Block_Widget_Container
{
    /** @var Mage_Adminhtml_Model_Session */
    protected $session;

    /** @var Mage_Core_Model_App */
    protected $app;
    
    /** @var Mage_Core_Model_Store */
    protected $store;

    /** @var Yireo_EmailTester_Helper_Data */
    protected $helper;

    /** @var Yireo_EmailTester_Helper_Output */
    protected $outputHelper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->app = Mage::app();
        $this->helper = Mage::helper('emailtester');
        $this->outputHelper = Mage::helper('emailtester/output');
        $this->session = Mage::getSingleton('admin/session');
        $this->store = Mage::getModel('core/store');
    }

    /**
     * @return Yireo_EmailTester_Helper_Data
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
