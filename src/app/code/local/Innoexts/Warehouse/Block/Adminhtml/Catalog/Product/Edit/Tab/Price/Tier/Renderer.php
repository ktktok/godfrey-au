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
 * Product tier price tab renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Tier_Renderer 
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier 
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('warehouse/catalog/product/edit/tab/price/tier/renderer.phtml');
    }
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
     * Get stock identifiers
     * 
     * @return array
     */
    public function getStockIds()
    {
        return $this->getWarehouseHelper()->getStockIds();
    }
    /**
     * Get warehouse title by stock identifier
     * 
     * @param int $stockId
     * 
     * @return string
     */
    public function getWarehouseTitleByStockId($stockId)
    {
        return $this->getWarehouseHelper()->getWarehouseTitleByStockId($stockId);
    }
    /**
     * Get default stock identifier
     * 
     * @return int
     */
    public function getDefaultStockId()
    {
        return 0;
    }
    /**
     * Check if group price is fixed
     * 
     * @return bool
     */
    public function isGroupPriceFixed()
    {
        return $this->getWarehouseHelper()->getProductHelper()->isGroupPriceFixed($this->getProduct());
    }
    /**
     * Get values
     * 
     * @return array
     */
    public function getValues()
    {
        $helper         = $this->getWarehouseHelper();
        $priceHelper    = $helper->getProductPriceHelper();
        $values         = array();
        $data           = $this->getElement()->getValue();
        if (is_array($data)) {
            usort($data, array($this, '_sortTierPrices'));
            $values     = $data;
        }
        $storeId    = $this->getProduct()->getStoreId();
        $websiteId  = $helper->getWebsiteIdByStoreId($storeId);
        $_values    = array();
        foreach ($values as $k => $v) {
            if (!$priceHelper->isInactiveData($v, $websiteId)) {
                $_values[$k] = $v;
            }
        }
        $values = $_values;
        foreach ($values as &$v) {
            $v['readonly'] = ($priceHelper->isAncestorData($v, $websiteId)) ? true : false;
        }
        return $values;
    }
    /**
     * Sort tier price values callback method
     *
     * @param array $a
     * @param array $b
     * 
     * @return int
     */
    protected function _sortTierPrices($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['stock_id'] != $b['stock_id']) {
            return $a['stock_id'] < $b['stock_id'] ? -1 : 1;
        }
        if ($a['cust_group'] != $b['cust_group']) {
            return $this->getCustomerGroups($a['cust_group']) < $this->getCustomerGroups($b['cust_group']) ? -1 : 1;
        }
        if ($a['price_qty'] != $b['price_qty']) {
            return $a['price_qty'] < $b['price_qty'] ? -1 : 1;
        }
        return 0;
    }
}