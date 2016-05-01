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
 * EmailTester Core model
 */
class Yireo_EmailTester_Model_Mailer_Variable_Product extends Varien_Object
{
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getVariable()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($this->getProductId());

        // Load the first product instead
        if (!$product->getId() > 0) {
            $product = Mage::getModel('catalog/product')->getCollection()->getFirstItem();
        }
        
        return $product;
    }
}