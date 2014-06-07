<?php
/**
 * Yireo EmailTester
 *
 * @author Yireo
 * @package EmailTester
 * @copyright Copyright 2014
 * @license Open Source License (OSL v3)
 * @link http://www.yireo.com
 */

/*
 * EmailTester observer to various Magento events
 */
class Yireo_EmailTester_Model_Observer extends Mage_Core_Model_Abstract
{
    /*
     * Method fired on the event <controller_action_predispatch>
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Yireo_EmailTester_Model_Observer
     */
    public function controllerActionPredispatch($observer)
    {
        // Run the feed
        Mage::getModel('emailtester/feed')->updateIfAllowed();
    }
}
