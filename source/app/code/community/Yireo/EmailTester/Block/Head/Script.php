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
 * Class Yireo_EmailTester_Block_Head_Script
 */
class Yireo_EmailTester_Block_Head_Script extends Mage_Adminhtml_Block_Widget_Container
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
     * Constructor method
     */
    public function _construct()
    {
        $this->url = Mage::getModel('adminhtml/url');
        $this->app = Mage::app();

        parent::_construct();

        $this->setTemplate('emailtester/head/script.phtml');
    }

    /**
     * Return the URL to send with
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->url->getUrl('adminhtml/emailtester/mail');
    }

    /**
     * Return the URL to
     *
     * @return string
     */
    public function getOutputUrl()
    {
        return $this->url->getUrl('adminhtml/emailtester/output');
    }

    /**
     * @return string
     */
    public function getAjaxOrderUrl()
    {
        return $this->url->getUrl('adminhtml/emailtester/ajax', array('type' => 'order'));
    }

    /**
     * @return string
     */
    public function getAjaxProductUrl()
    {
        return $this->url->getUrl('adminhtml/emailtester/ajax', array('type' => 'product'));
    }

    /**
     * @return string
     */
    public function getAjaxCustomerUrl()
    {
        return $this->url->getUrl('adminhtml/emailtester/ajax', array('type' => 'customer'));
    }

    /**
     * Return the default store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        $websites = $this->app->getWebsites(true);
        if (empty($websites[1]) || !is_object($websites[1])) {
            return 0;
        }

        /** @var Mage_Core_Model_Website $website */
        $website = $websites[1];
        $defaultStore = $website->getDefaultStore();
        if (empty($defaultStore)) {
            return 0;
        }

        return $defaultStore->getId();
    }
}