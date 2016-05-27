<?php
/**
 * Yireo EmailTester
 *
 * @author Yireo
 * @package EmailTester
 * @copyright Copyright 2015
 * @license Open Source License (OSL v3)
 * @link https://www.yireo.com
 */

/**
 * EmailTester observer to add the JS block to the header
 */
class Yireo_EmailTester_Observer_AddJsBlock
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $request;

    /**
     * @var Mage_Core_Model_Layout
     */
    protected $layout;

    /**
     * Yireo_EmailTester_Observer_AddJsBlock constructor.
     */
    public function __construct()
    {
        $this->request = Mage::app()->getRequest();
        $this->layout = Mage::app()->getLayout();
    }

    /**
     * Listen to the event core_block_abstract_to_html_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Yireo_EmailTester_Observer_AddJsBlock
     * @event core_block_abstract_to_html_before
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($this->allowBlock($block) == false) {
            return $this;
        }

        if ($this->allowRequest() == false) {
            return $this;
        }

        // Insert the JavaScript in the bottom of the page
        $this->insertScriptBlock();

        return $this;
    }

    /**
     * Allow modification of this block
     * 
     * @param $block Mage_Core_Block_Abstract
     *
     * @return bool
     */
    protected function allowBlock($block)
    {
        $blockName = $block->getNameInLayout();
        if ($blockName !== 'root') {
            return false;
        }

        return true;
    }

    /**
     * Allow modification of this request
     * 
     * @return bool
     */
    protected function allowRequest()
    {
        $controller = $this->request->getControllerName();
        $action = $this->request->getActionName();

        if ($controller == 'emailtester') {
            return true;
        }

        if ($controller == 'sales_order' && $action == 'view') {
            return true;
        }

        return false;
    }

    /**
     * Insert the JavaScript block in the footer
     */
    protected function insertScriptBlock()
    {
        $jsBlock = $this->getScriptBlock();
        $this->layout->getBlock('before_body_end')->insert($jsBlock);
    }
    
    /**
     * Get the script block
     *
     * @return Mage_Core_Block_Template
     */
    protected function getScriptBlock()
    {
        $jsBlock = $this->layout->createBlock('emailtester/head_script');
        
        return $jsBlock;
    }
}