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
        $this->setProduct(Mage::getModel('catalog/product')->load($productId));
    }

    public function getProductId()
    {
        $userData = Mage::getSingleton('adminhtml/session')->getData();
        $currentValue = (isset($userData['emailtester.product_id'])) ? (int)$userData['emailtester.product_id'] : null;
        if(empty($currentValue)) {
            $currentValue = Mage::getStoreConfig('emailtester/settings/default_product');
        }
        return $currentValue;
    }

    public function getProductOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $currentValue = $this->getProductId();
        $limit = Mage::getStoreConfig('emailtester/settings/limit_product');

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC')
        ;

        if($limit > 0) $products->setPage(0, $limit);

        $customOptions = $this->getCustomOptions('product');
        if(!empty($customOptions)) {
            $products->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        foreach($products as $product) {
            $value = $product->getId();
            $label = '['.$product->getId().'] '.$product->getName();
            $current = ($product->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }
    
    public function getProductSearch()
    {
        $productId = $this->getProductId(); 
        if(!empty($productId)) {
            $product = Mage::getModel('catalog/product')->load($productId);
            return Mage::helper('emailtester')->getProductOutput($product);
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

