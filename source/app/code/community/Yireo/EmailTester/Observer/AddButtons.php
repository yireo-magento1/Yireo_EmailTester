<?php
/**
 * Yireo EmailTester
 *
 * @author Yireo
 * @package EmailTester
 * @copyright Copyright 2015
 * @license Open Source License (OSL v3)
 * @link https://www.yireo.com
 */

/**
 * EmailTester observer to add buttons to order view page
 */
class Yireo_EmailTester_Observer_AddButtons
{
    /**
     * @var Yireo_EmailTester_Helper_Data
     */
    protected $helper;

    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $request;
    
    /**
     * Yireo_EmailTester_Observer_AddButtons constructor.
     */
    public function __construct()
    {
        $this->request = Mage::app()->getRequest();
        $this->helper = Mage::helper('emailtester');
    }

    /**
     * Listen to the event adminhtml_widget_container_html_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Yireo_EmailTester_Observer_AddButtons
     * @event adminhtml_widget_container_html_before
     */
    public function adminhtmlWidgetContainerHtmlBefore($observer)
    {
        if ($this->helper->getStoreConfig('emailtester/settings/show_order_button') == 0) {
            return $this;
        }
        
        $event = $observer->getEvent();
        $block = $event->getBlock();

        if (!$block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            return $this;
        }
        
        $orderId = $this->request->getParam('order_id');
        $emailtesterLink = $this->helper->getTesterLink($orderId);

        $block->addButton('emailtester_link', array(
            'label' => $this->helper->__('Test Email'),
            'onclick' => 'setLocation(\'' . $emailtesterLink . '\');',
            'class' => 'go',
        ));

        return $this;
    }
}