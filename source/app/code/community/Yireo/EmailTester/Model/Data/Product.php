<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

/**
 * Class Yireo_EmailTester_Model_Data_Product
 */
class Yireo_EmailTester_Model_Data_Product extends Yireo_EmailTester_Model_Data_Generic
{
    /** @var Mage_Catalog_Model_Product */
    protected $productModel;

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->productModel = Mage::getModel('catalog/product');
    }


    /**
     * @param int $productId
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getProduct($productId)
    {
        return $this->productModel->load($productId);
    }

    /**
     * Return the current product ID
     *
     * @return int
     */
    public function getProductId()
    {
        $productId = $this->request->getParam('product_id', 0);
        if (!empty($productId)) {
            return $productId;
        }

        $userData = $this->session->getData();
        $productId = (isset($userData['emailtester.product_id'])) ? (int)$userData['emailtester.product_id'] : null;

        if(!empty($productId)) {
            return $productId;
        }

        $productId = $this->getStoreConfig('emailtester/settings/default_product');
        return $productId;
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
        $products = $this->getProductCollection();

        foreach($products as $product) {
            /** @var Mage_Catalog_Model_Product $product */
            $value = $product->getId();
            $label = '['.$product->getId().'] '.$this->outputHelper->getProductOutput($product);
            $current = ($product->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getProductCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
        $products = $this->productModel->getCollection()
            ->addAttributeToSelect('*')
            ->setOrder('entity_id', 'DESC')
        ;

        $limit = $this->getStoreConfig('emailtester/settings/limit_product');
        if($limit > 0) {
            $products->setPage(0, $limit);
        }

        $customOptions = $this->getCustomOptions('product');
        if(!empty($customOptions)) {
            $products->addAttributeToFilter('entity_id', array('in' => $customOptions));
        }

        return $products;
    }

    /**
     * Get current product result
     *
     * @return string
     */
    public function getProductSearch()
    {
        $productId = $this->getProductId();
        if($this->isValidId($productId)) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = $this->productModel->load($productId);
            return $this->outputHelper->getProductOutput($product);
        }

        return '';
    }
}