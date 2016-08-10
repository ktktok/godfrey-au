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
 * @package     Innoexts_WarehouseEnterprise
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Admin cart
 *
 * @category   Innoexts
 * @package    Innoexts_WarehouseEnterprise
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_WarehouseEnterprise_Model_Enterprise_Checkout_Cart 
    extends Enterprise_Checkout_Model_Cart 
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
     * Get version helper
     * 
     * @return Innoexts_Core_Helper_Version
     */
    protected function getVersionHelper()
    {
        return $this->getWarehouseHelper()
            ->getVersionHelper();
    }
    /**
     * Get catalog inventory helper
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    protected function getCatalogInventoryHelper()
    {
        return $this->getWarehouseHelper()
            ->getCatalogInventoryHelper();
    }
    /**
     * Returns suggested quantities for items.
     *
     * @param   array $data
     * 
     * @return  array
     */
    public function suggestItemsQty($data)
    {
        foreach ($data as $itemId => $itemInfo) {
            if (!isset($itemInfo['qty'])) {
                continue;
            }
            $qty = (float) $itemInfo['qty'];
            if ($qty <= 0) {
                continue;
            }
            $quoteItem = $this->getQuote()->getItemById($itemId);
            if (!$quoteItem) {
                continue;
            }
            $stockItem = $quoteItem->getStockItem();
            if (!$stockItem) {
                continue;
            }
            $product = $quoteItem->getProduct();
            if (!$product) {
                continue;
            }
            $data[$itemId]['before_suggest_qty'] = $qty;
            $data[$itemId]['qty'] = $stockItem->suggestQty($qty);
        }
        return $data;
    }
    /**
     * Update item in shopping cart
     *
     * @param int $id
     * @param int|array|Varien_Object $requestInfo
     * @param null|array|Varien_Object $updatingParams
     * @return Mage_Sales_Model_Quote_Item|string
     */
    public function updateItem($itemId, $requestInfo = null, $updatingParams = null)
    {
        try {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                Mage::throwException(Mage::helper('checkout')->__('Quote item does not exist.'));
            }
            $productId = $item->getProduct()->getId();
            $product = $this->_getProduct($productId);
            $request = $this->_getProductRequest($requestInfo);
            $stockItem = $item->getStockItem();
            if ($stockItem) {
                $minimumQty = $stockItem->getMinSaleQty();
                if ($minimumQty && ($minimumQty > 0) && ($request->getQty() < $minimumQty) && !$this->getQuote()->hasProductId($productId)) {
                    $request->setQty($minimumQty);
                }
            }
            $result = $this->getQuote()->updateItem($itemId, $request, $updatingParams);
        } catch (Mage_Core_Exception $e) {
            $this->getCheckoutSession()->setUseNotice(false);
            $result = $e->getMessage();
        }
        if (is_string($result)) {
            if ($this->getCheckoutSession()->getUseNotice() === null) {
                $this->getCheckoutSession()->setUseNotice(true);
            }
            Mage::throwException($result);
        }
        Mage::dispatchEvent('checkout_cart_product_update_after', array('quote_item' => $result, 'product' => $product));
        $this->getCheckoutSession()->setLastAddedProductId($productId);
        return $result;
    }
    /**
     * Update cart items information
     *
     * @param array $data
     * 
     * @return self
     */
    public function updateItems($data)
    {
        $helper         = $this->getWarehouseHelper();
        $config         = $helper->getConfig();
        $productHelper  = $helper->getProductHelper();
        
        Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$this, 'info'=>$data));
        $messageFactory = Mage::getSingleton('core/message');
        $session = $this->getCheckoutSession();
        $qtyRecalculatedFlag = false;
        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }
            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                $this->removeItem($itemId);
                continue;
            }
            $qty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            if ($qty > 0) {
                
                if ($config->isAllowAdjustment() && isset($itemInfo['stock_id'])) {
                    $product    = $item->getProduct();
                    $stockId    = (int) $itemInfo['stock_id'];
                    if ($helper->isStockIdExists($stockId)) {
                        $productHelper->setSessionStockId($product, $stockId);
                    }
                }
                
                $item->setQty($qty);
                
                if ($this->getVersionHelper()->isGe1700()) {
                    $itemInQuote = $this->getQuote()->getItemById($item->getId());
                    if (!$itemInQuote && $item->getHasError()) {
                        Mage::throwException($item->getMessage());
                    }
                } else {
                    if ($item->getHasError()) {
                        Mage::throwException($item->getMessage());
                    }
                }
                
                if (isset($itemInfo['before_suggest_qty']) && ($itemInfo['before_suggest_qty'] != $qty)) {
                    $qtyRecalculatedFlag = true;
                    $message = $messageFactory->notice(Mage::helper('checkout')->__('Quantity was recalculated from %d to %d', $itemInfo['before_suggest_qty'], $qty));
                    $session->addQuoteItemMessage($item->getId(), $message);
                }
            }
        }
        if ($qtyRecalculatedFlag) {
            $session->addNotice(
                Mage::helper('checkout')->__('Some products quantities were recalculated because of quantity increment mismatch')
            );
        }
        Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
        return $this;
    }
    /**
     * Reset stocks
     *
     * @param array $data
     * 
     * @return self
     */
    public function resetStocks($data)
    {
        $helper         = $this->getWarehouseHelper();
        $productHelper  = $helper->getProductHelper();
        $quote          = $this->getQuote();
        foreach ($data as $itemId => $itemInfo) {
            $item = $quote->getItemById($itemId);
            if (!$item) {
                continue;
            }
            $product = $item->getProduct();
            $productHelper->setSessionStockId($product, null);
        }
        $quote->reapplyStocks();
        return $this;
    }
}