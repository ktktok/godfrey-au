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
 * Order item
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Sales_Order_Item 
    extends Mage_Sales_Model_Order_Item 
{
    /**
     * Stock item model
     *
     * @var Mage_CatalogInventory_Model_Stock_Item
     */
    protected $_stockItem;
    /**
     * Warehouse
     *
     * @var Innoexts_Warehouse_Model_Warehouse
     */
    protected $_warehouse;
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Get catalog inventory helper
     * 
     * @return Innoexts_Warehouse_Helper_Cataloginventory
     */
    protected function getCatalogInventoryHelper()
    {
        return $this->getWarehouseHelper()->getCatalogInventoryHelper();
    }
    /**
     * Retrieve warehouse
     *
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    public function getWarehouse()
    {
        if (is_null($this->_warehouse)) {
            if ($this->getStockId()) {
                $this->_warehouse = $this->getWarehouseHelper()->getWarehouseByStockId($this->getStockId());
            }
        }
        return $this->_warehouse;
    }
    /**
     * Get warehouse title
     * 
     * @return string
     */
    public function getWarehouseTitle()
    {
        $warehouse = $this->getWarehouse();
        if ($warehouse) {
            return $warehouse->getTitle();
        } else {
            return null;
        }
    }
    /**
     * Get warehouse description
     * 
     * @return string
     */
    public function getWarehouseDescription()
    {
        $warehouse = $this->getWarehouse();
        if ($warehouse) {
            return $warehouse->getDescription();
        } else {
            return null;
        }
    }
    /**
     * Get product
     * 
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return $this->_getData('product');
    }
    /**
     * Set product
     * 
     * @param Mage_Catalog_Model_Product $product
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    protected function _setProduct($product)
    {
        $this->setData('product', $product);
        return $this;
    }
    /**
     * Get stock identifier
     * 
     * @return int
     */
    protected function _getStockId()
    {
        return $this->_getData('stock_id');
    }
    /**
     * Set stock identifier
     * 
     * @param int $stockId
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    protected function _setStockId($stockId)
    {
        $this->setData('stock_id', $stockId);
        return $this;
    }
    /**
     * Get stock item
     * 
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    protected function _getStockItem()
    {
        return $this->_stockItem;
    }
    /**
     * Set stock item
     * 
     * @param Innoexts_Warehouse_Model_Cataloginventory_Stock_Item $stockItem
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    protected function _setStockItem($stockItem)
    {
        $this->_stockItem = $stockItem;
        return $this;
    }
    /**
     * Unset stock item
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    protected function _unsetStockItem()
    {
        if (!is_null($this->_stockItem)) {
            $this->_stockItem = null;
        }
        return $this;
    }
    /**
     * Retrieve product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (($product === null) && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            $this->setProduct($product);
        }
        return $product;
    }
    /**
     * Set product
     * 
     * @param   Mage_Catalog_Model_Product $product
     * 
     * @return  Innoexts_Warehouse_Model_Sales_Order_Item
     */
    public function setProduct($product)
    {
        $this->_unsetStockItem();
        $this->_setProduct($product);
        $this->getStockItem();
        parent::setProduct($product);
        return $this;
    }
    /**
     * Set stock identifier
     * 
     * @param int $stockId
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    public function setStockId($stockId)
    {
        $this->_unsetStockItem();
        $this->_setStockId($stockId);
        $this->getStockItem();
        return $this;
    }
    /**
     * Get stock item
     * 
     * @return Innoexts_Warehouse_Model_Cataloginventory_Stock_Item
     */
    public function getStockItem()
    {
        $stockItem = $this->_getStockItem();
        if (!$stockItem) {
            $stockId = $this->_getStockId();
            if (!$stockId) {
                $product = $this->_getProduct();
                if ($product) {
                    $stockItem = $product->getStockItem();
                    if ($stockItem) {
                        $this->setStockItem($stockItem);
                        return $this->_getStockItem();
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            } else {
                $stockItem = $this->getCatalogInventoryHelper()->getStockItem($stockId);
                $this->setStockItem($stockItem);
                return $this->_getStockItem();
            }
        } else {
            return $this->_getStockItem();
        }
    }
    /**
     * Set stock item
     * 
     * @param Innoexts_Warehouse_Model_Cataloginventory_Stock_Item $stockItem
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    public function setStockItem($stockItem)
    {
        if ($stockItem && $stockItem->getStockId()) {
            $stockId = $stockItem->getStockId();
            $this->_setStockId($stockId);
            $this->_setStockItem($stockItem);
            $product = $this->_getProduct();
            if ($product) {
                $stockItem->assignProduct($product);
            }
        }
        return $this;
    }
    /**
     * Clear order object data
     * 
     * @param string $key data key
     * 
     * @return Innoexts_Warehouse_Model_Sales_Order_Item
     */
    public function unsetData($key=null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_stockItem = null;
        }
        return $this;
    }
}