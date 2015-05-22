<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (http://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (http://www.yireo.com/)
 * @license     Open Source License
 */

class Yireo_EmailTester_Block_Form_Product extends Yireo_EmailTester_Block_Form_Abstract
{
    /*
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/form/product.phtml');

        $productId = $this->getRequest()->getParam('product_id', 0);
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->setData('product', $product);
    }

    /**
     * Return the current product ID
     *
     * @return int
     */
    public function getProductId()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.product_id'])) ? (int)$userData['emailtester.product_id'] : null;

        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_product');
        }

        return $currentValue;
    }

    /**
     * Return a list of product select options
     *
     * @return array
     */
    public function getProductOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $currentValue = $this->getProductId();
        $limit = Mage::getStoreConfig('emailtester/settings/limit_product');

        /* @var Mage_Catalog_Model_Resource_Product_Collection $products */
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC')
        ;

        if($limit > 0) {
            $products->setPage(0, $limit);
        }

        $customOptions = $this->getCustomOptions('product');
        if(!empty($customOptions)) {
            $products->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        foreach($products as $product) {
            /* @var Mage_Catalog_Model_Product $product */
            $value = $product->getId();
            $label = '['.$product->getId().'] '.$this->helper->getProductOutput($product);
            $current = ($product->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    /**
     * Get current product result
     *
     * @return string
     */
    public function getProductSearch()
    {
        $productId = $this->getProductId(); 
        if(!empty($productId)) {
            $product = Mage::getModel('catalog/product')->load($productId);
            return $this->helper->getProductOutput($product);
        }
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $productsGrid = $this->getLayout()->createBlock('adminhtml/catalog_product_widget_chooser', '', array(
            'id' => 'productId',
            'use_massaction' => false,
            'product_type_id' => null,
            'category_id' => $this->getRequest()->getParam('category_id')
        ));
        
        $this->setChild('productsGrid', $productsGrid);

        return parent::_toHtml();
    }
}

