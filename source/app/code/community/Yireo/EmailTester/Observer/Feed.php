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
 * EmailTester observer to load Yireo feed
 */
class Yireo_EmailTester_Observer_Feed
{
    /**
     * @var Yireo_EmailTester_Model_Feed
     */
    protected $feedModel;

    /**
     * Yireo_EmailTester_Observer_Feed constructor.
     */
    public function __construct()
    {
        $this->feedModel = Mage::getModel('emailtester/feed');
    }

    /**
     * Method fired on the event <controller_action_predispatch>
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Yireo_EmailTester_Observer_Feed
     * @event controller_action_predispatch
     */
    public function controllerActionPredispatch($observer)
    {
        // Run the feed
        $this->feedModel->updateIfAllowed();

        return $this;
    }
}