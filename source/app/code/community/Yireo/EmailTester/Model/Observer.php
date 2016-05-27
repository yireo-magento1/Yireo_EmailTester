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
 * EmailTester observer to various Magento events
 * 
 * @deprecated
 */
class Yireo_EmailTester_Model_Observer
{
    /**
     * @param $observer
     *
     * @return $this
     * @deprecated
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        return $this;
    }
    
    /**
     * @param $observer
     *
     * @return $this
     * @deprecated
     */
    public function controllerActionPredispatch($observer)
    {
        return $this;
    }
}
