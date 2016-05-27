<?php
/**
 * Yireo EmailTester for Magento 
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

/**
 * Class Yireo_EmailTester_Block_Form_Product
 */
class Yireo_EmailTester_Block_Form_Product extends Yireo_EmailTester_Block_Form_Abstract
{
    /**
     * @var Yireo_EmailTester_Model_Data_Product
     */
    protected $productData;
    
    /**
     * Constructor method
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('emailtester/form/product.phtml');

        $this->productData = Mage::getModel('emailtester/data_product');
    }

    /**
     * Return the current product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productData->getProductId();
    }

    /**
     * Return a list of product select options
     *
     * @return array
     */
    public function getProductOptions()
    {
        return $this->productData->getProductOptions();
    }

    /**
     * Get current product result
     *
     * @return string
     */
    public function getProductSearch()
    {
        return $this->productData->getProductSearch();
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

