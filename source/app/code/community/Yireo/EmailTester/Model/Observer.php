<?php
/**
 * Yireo EmailTester
 *
 * @author Yireo
 * @package EmailTester
 * @copyright Copyright 2015
 * @license Open Source License (OSL v3)
 * @link http://www.yireo.com
 */

/*
 * EmailTester observer to various Magento events
 */
class Yireo_EmailTester_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Listen to the event core_block_abstract_to_html_before
     *
     * @parameter Varien_Event_Observer $observer
     * @return $this
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $blockName = $block->getNameInLayout();
        if($blockName != 'root') {
            return $this;
        }

        $controller = Mage::app()->getRequest()->getControllerName();
        if($controller != 'emailtester') {
            return $this;
        }

        // Insert the JavaScript in the bottom of the page
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        $jsBlock = $layout->createBlock('core/template');
        $jsBlock->setTemplate('emailtester/head/script.phtml');
        $layout->getBlock('before_body_end')->insert($jsBlock);

        return $this;
    }

    /*
     * Method fired on the event <controller_action_predispatch>
     *
     * @param Varien_Event_Observer $observer
     * @return Yireo_EmailTester_Model_Observer
     */
    public function controllerActionPredispatch($observer)
    {
        // Run the feed
        Mage::getModel('emailtester/feed')->updateIfAllowed();
    }
}
