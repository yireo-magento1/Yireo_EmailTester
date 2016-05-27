<?php

/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */
class Yireo_EmailTester_Block_Form extends Mage_Adminhtml_Block_Widget_Container
{
    /** @var Mage_Adminhtml_Model_Url */
    protected $url;

    /** @var Mage_Adminhtml_Model_Session */
    protected $session;

    /** @var Mage_Core_Model_App */
    protected $app;

    /** @var Mage_Core_Model_Config */
    protected $config;

    /** @var Yireo_EmailTester_Helper_Data */
    protected $helper;

    /**
     * Constructor method
     */
    public function _construct()
    {
        $this->url = Mage::getModel('adminhtml/url');
        $this->session = Mage::getSingleton('admin/session');
        $this->app = Mage::app();
        $this->config = $this->app->getConfig();
        $this->helper = Mage::helper('emailtester');

        parent::_construct();

        $this->setTemplate('emailtester/form.phtml');
    }


    /**
     * Return the current store ID
     *
     * @return int
     * @throws Exception
     */
    public function getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        if (!$storeId > 0) {
            $storeId = $this->session->getData('emailtester.store');
        }

        $this->session->setData('emailtester.store', $storeId);
        return $storeId;
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
     * Return the delete URL
     *
     * @return string
     */
    public function getVersion()
    {
        $config = $this->config->getModuleConfig('Yireo_EmailTester');
        return (string)$config->version;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setChild('accordion', $this->getAccordionBlock());
        $this->setChild('output_button', $this->getOutputButtonBlock());
        $this->setChild('send_button', $this->getSendButtonBlock());
        $this->setChild('check', $this->getCheckBlock());

        $rt = parent::_toHtml();

        return $rt;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Accordion
     */
    protected function getAccordionBlock()
    {
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')->setId('emailtester');

        $accordion->addItem('generic', array(
            'title' => $this->helper->__('Generic'),
            'content' => $this->getLayout()->createBlock('emailtester/form_generic')->toHtml(),
            'open' => true,
        ));

        $accordion->addItem('customer', array(
            'title' => $this->helper->__('Customer'),
            'content' => $this->getLayout()->createBlock('emailtester/form_customer')->toHtml(),
            'open' => true,
        ));

        $accordion->addItem('product', array(
            'title' => $this->helper->__('Product'),
            'content' => $this->getLayout()->createBlock('emailtester/form_product')->toHtml(),
            'open' => true,
        ));

        $accordion->addItem('order', array(
            'title' => $this->helper->__('Order'),
            'content' => $this->getLayout()->createBlock('emailtester/form_order')->toHtml(),
            'open' => true,
        ));
        
        return $accordion;
    }

    /**
     * @return Yireo_EmailTester_Block_Form_Check
     */
    protected function getCheckBlock()
    {
        return $this->getLayout()->createBlock('emailtester/form_check')->setId('check');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    protected function getOutputButtonBlock()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => $this->helper->__('Print Email'),
                'onclick' => 'emailtesterPrint()',
                'class' => 'save',
            ));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Button
     */
    protected function getSendButtonBlock()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => $this->helper->__('Send Email'),
                'onclick' => 'emailtesterEmail()',
                'class' => 'save',
            ));
    }

    /**
     * Prepare the layout
     *
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        $this->getRequest()->setParam('store', $this->getStore());

        /** @var Mage_Adminhtml_Block_Store_Switcher $storeSwitcherBlock */
        $storeSwitcherBlock = $this->getLayout()->createBlock('adminhtml/store_switcher')
            ->setUseConfirm(false)
            ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)));

        $this->setChild('store_switcher', $storeSwitcherBlock);

        return parent::_prepareLayout();
    }
}
