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
 * EmailTester helper
 */
class Yireo_EmailTester_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mage_Adminhtml_Model_Url
     */
    protected $url;

    /**
     * @var Mage_Core_Model_App
     */
    protected $app;
    
    /**
     * Yireo_EmailTester_Helper_Data constructor.
     */
    public function __construct()
    {
        $this->app = Mage::app();
        $this->url = Mage::getModel('adminhtml/url');
    }
    
    /**
     * Switch to determine whether this extension is enabled or not
     *
     * @return bool
     */
    public function enabled()
    {
        if ((bool)$this->getStoreConfig('advanced/modules_disable_output/Yireo_EmailTester')) {
            return false;
        }

        return $this->getStoreConfig('emailtester/settings/enabled');
    }

    /**
     * @return string
     */
    public function getTesterLink($orderId)
    {
        return $this->url->getUrl('adminhtml/emailtester/index', array('order_id' => $orderId, 'product_id' => 'X', 'customer_id' => 'X'));
    }

    /**
     * Return the default email
     *
     * @return string
     */
    public function getDefaultEmail()
    {
        return $this->getStoreConfig('emailtester/settings/default_email');
    }
    
    /**
     * @param $value
     *
     * @return null|string
     */
    public function getStoreConfig($value)
    {
        return $this->app->getStore()->getConfig($value);
    }
}
