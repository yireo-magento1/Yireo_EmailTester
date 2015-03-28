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
     *
     * @access public
     * @param null
     * @return null
     */
    public function _construct()
    {
        $this->setTemplate('emailtester/form.phtml');
        parent::_construct();
    }

    public function getSendUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/emailtester/mail');
    }

    public function getOutputUrl()
    {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/emailtester/output');
    }

    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if (!$storeId > 0) {
            $storeId = Mage::getSingleton('adminhtml/session')->getData('emailtester.store');
        }

        Mage::getSingleton('adminhtml/session')->setData('emailtester.store', $storeId);
        return $storeId;
    }

    public function getDefaultStoreId()
    {
        $websites = Mage::app()->getWebsites(true);
        return $websites[1]->getDefaultStore()->getId();
    }

    /**
     * Return the delete URL
     *
     * @access public
     * @param null
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

        $this->setChild('output_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                 ->setData(array(
                     'label' => Mage::helper('emailtester')->__('Print Email'),
                     'onclick' => 'emailtesterPrint()',
                     'class' => 'save',
                 ))
        );

        $this->setChild('send_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                 ->setData(array(
                     'label' => Mage::helper('emailtester')->__('Send Email'),
                     'onclick' => 'emailtesterEmail()',
                     'class' => 'save',
                 ))
        );

        $check = $this->getLayout()->createBlock('emailtester/form_check')->setId('check');
        $this->setChild('check', $check);

        $rt = parent::_toHtml();

        return $rt;
    }

    protected function _prepareLayout()
    {
        $this->getRequest()->setParam('store', $this->getStore());
        $block = $this->getLayout()->createBlock('adminhtml/store_switcher')
                      ->setUseConfirm(false)
                      ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)));
        $this->setChild('store_switcher', $block);
        return parent::_prepareLayout();
    }
}
