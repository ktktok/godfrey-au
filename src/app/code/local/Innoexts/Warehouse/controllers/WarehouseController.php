<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */
/**
 * Warehouse controller
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_WarehouseController 
    extends Mage_Core_Controller_Front_Action 
{
    /**
     * Get warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Set stock
     */
    public function setStockAction()
    {
        $helper     = $this->getWarehouseHelper();
        $config     = $helper->getConfig();
        if (!$config->isMultipleMode() && $config->isAllowAdjustment()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $coreSession    = $helper->getCoreSession();
                if ($request->getParam('set_stock') == 'update') {
                    $stockId        = (int) $request->getParam('stock_id');
                    if ($helper->isStockIdExists($stockId)) {
                        $helper->setSessionStockId($stockId);
                        $coreSession->addSuccess($helper->__('Warehouse has been updated.'));
                    }
                } else {
                    $helper->removeSessionStockId();
                    $coreSession->addSuccess($helper->__('Warehouse has been reset.'));
                }
            }
        }
        $this->_redirectReferer();
    }
    /**
     * Refresh product quote
     */
    public function refreshProductQuoteAction()
    {
        $helper     = $this->getWarehouseHelper();
        $config     = $helper->getConfig();
        $result     = array();
        $request    = $this->getRequest();
        if ($request->isPost()) {
            $productId      = (int) $request->getParam('product');
            $params         = new Varien_Object();
            $buyRequest     = new Varien_Object();
            $buyRequest->setData($request->getParams());
            $params->setBuyRequest($buyRequest);
            $productHelper  = Mage::helper('catalog/product');
            $product        = $productHelper->initProduct($productId, $this, $params);
            if ($product) {
                $productHelper->prepareProductOptions($product, $buyRequest);
                Mage::dispatchEvent('catalog_controller_product_view', array('product' => $product));
                $productQuoteBlock  = $this->getLayout()->createBlock('warehouse/catalog_product_view_quote');
                $result['error']    = false;
                $result['html']     = $productQuoteBlock->toHtml();
            } else {
                $result['error']    = true;
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
    /**
     * Update product quote
     */
    public function updateProductQuoteAction()
    {
        $helper     = $this->getWarehouseHelper();
        $config     = $helper->getConfig();
        if ($config->isMultipleMode() && $config->isAllowAdjustment()) {
            $request = $this->getRequest();
            if ($request->isPost()) {
                $productId      = (int) $request->getParam('product');
                $product        = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                if ($product->getId()) {
                    $coreSession    = $helper->getCoreSession();
                    if ($request->getParam('update_product_quote') == 'update') {
                        $stockId        = (int) $request->getParam('stock_id');
                        if ($helper->isStockIdExists($stockId)) {
                            $helper->getProductHelper()->setSessionStockId($product, $stockId);
                            $coreSession->addSuccess($helper->__('Product quote has been updated.'));
                        }
                    } else {
                        $helper->getProductHelper()->setSessionStockId($product, null);
                        $coreSession->addSuccess($helper->__('Product quote has been reset.'));
                    }
                }
            }
        }
        $this->_redirectReferer();
    }
}