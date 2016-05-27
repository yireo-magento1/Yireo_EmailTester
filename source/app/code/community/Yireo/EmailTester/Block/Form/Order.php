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
 * Class Yireo_EmailTester_Block_Form_Order
 */
class Yireo_EmailTester_Block_Form_Order extends Yireo_EmailTester_Block_Form_Abstract
{
    /**
     * @var Yireo_EmailTester_Model_Data_Order
     */
    protected $orderData;


    /**
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/form/order.phtml');
        
        $this->orderData = Mage::getModel('emailtester/data_order');
    }
    
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Get the current order ID
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderData->getOrderId();
    }

    /**
     * Get an array of order select options
     *
     * @return array
     */
    public function getOrderOptions()
    {
        return $this->orderData->getOrderOptions();
    }

    /**
     * Get current order result
     *
     * @return string
     */
    public function getOrderSearch()
    {
        return $this->orderData->getOrderSearch();
    }
}
