<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

class Yireo_EmailTester_Block_Form extends Mage_Adminhtml_Block_Widget_Container
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        $this->setTemplate('emailtester/form.phtml');

        parent::_construct();
    }

    /**
     * Return the URL to send with
     *
     * @return string
     */
    public function getSendUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/emailtester/mail');
    }

    /**
     * Return the URL to
     *
     * @return string
     */
    public function getOutputUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/emailtester/output');
    }

    /**
     * Return the current store ID
     *
     * @return int
     * @throws Exception
     */
    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if (!$storeId > 0) {
            $storeId = Mage::getSingleton('adminhtml/session')->getData('emailtester.store');
        }

        Mage::getSingleton('adminhtml/session')->setData('emailtester.store', $storeId);
        return $storeId;
    }

    /**
     * Return the default store ID
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        $websites = Mage::app()->getWebsites(true);
        if (empty($websites[1]) || !is_object($websites[1])) {
            return 0;
        }

        $defaultStore = $websites[1]->getDefaultStore();
        if (empty($defaultStore)) {
            return 0;
        }

        return $defaultStore->getId();
    }

    /**
     * Return the delete URL
     *
     * @return string
     */
    public function getVersion()
    {
        $config = Mage::app()->getConfig()->getModuleConfig('Yireo_EmailTester');
        return (string) $config->version;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var Mage_Adminhtml_Block_Widget_Accordion $accordion */
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')->setId('emailtester');

        $accordion->addItem('generic', array(
            'title' => Mage::helper('emailtester')->__('Generic'),
            'content' => $this->getLayout()->createBlock('emailtester/form_generic')->toHtml(),
            'open' => true,
        ));

        $accordion->addItem('customer', array(
            'title' => Mage::helper('emailtester')->__('Customer'),
            'content' => $this->getLayout()->createBlock('emailtester/form_customer')->toHtml(),
            'open' => true,
        ));

        $accordion->addItem('product', array(
            'title' => Mage::helper('emailtester')->__('Product'),
            'content' => $this->getLayout()->createBlock('emailtester/form_product')->toHtml(),
            'open' => true,
        ));

        $accordion->addItem('order', array(
            'title' => Mage::helper('emailtester')->__('Order'),
            'content' => $this->getLayout()->createBlock('emailtester/form_order')->toHtml(),
            'open' => true,
        ));

        $this->setChild('accordion', $accordion);

        /* @var Mage_Adminhtml_Block_Widget_Button $outputButton */
        $outputButton =  $this->getLayout()->createBlock('adminhtml/widget_button')
                 ->setData(array(
                     'label' => Mage::helper('emailtester')->__('Print Email'),
                     'onclick' => 'emailtesterPrint()',
                     'class' => 'save',
                 ));
        $this->setChild('output_button', $outputButton);

        /* @var Mage_Adminhtml_Block_Widget_Button $sendButton */
        $sendButton = $this->getLayout()->createBlock('adminhtml/widget_button')
                 ->setData(array(
                     'label' => Mage::helper('emailtester')->__('Send Email'),
                     'onclick' => 'emailtesterEmail()',
                     'class' => 'save',
                 ));
        $this->setChild('send_button', $sendButton);

        /* @var Yireo_EmailTester_Block_Form_Check $checkBlock */
        $checkBlock = $this->getLayout()->createBlock('emailtester/form_check')->setId('check');
        $this->setChild('check', $checkBlock);

        $rt = parent::_toHtml();

        return $rt;
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

        /* @var Mage_Adminhtml_Block_Store_Switcher $storeSwitcherBlock */
        $storeSwitcherBlock = $this->getLayout()->createBlock('adminhtml/store_switcher')
                      ->setUseConfirm(false)
                      ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)));

        $this->setChild('store_switcher', $storeSwitcherBlock);

        return parent::_prepareLayout();
    }
}
