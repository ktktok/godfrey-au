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
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Creditmemo grid collection
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Mysql4_Sales_Order_Creditmemo_Grid_Collection 
    extends Mage_Sales_Model_Mysql4_Order_Creditmemo_Grid_Collection
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
     * Adding stock titles to result collection
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Sales_Order_Shipment_Grid_Collection
     */
    public function addStockTitlesToResult()
    {
        $creditmemoStocks = array();
        foreach ($this as $creditmemo) { 
            $creditmemoStocks[$creditmemo->getId()] = array(); 
        }
        if (!empty($creditmemoStocks)) {
            $select = $this->getConnection()->select()
                ->from(array('creditmemo_stock' => $this->getTable('warehouse/creditmemo_grid_warehouse')))
                ->join(array('stock' => $this->getTable('cataloginventory/stock')), 'stock.stock_id = creditmemo_stock.stock_id', array('stock_name'))
                ->where($this->getConnection()->quoteInto('creditmemo_stock.entity_id IN (?)', array_keys($creditmemoStocks)));
            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) { 
                $creditmemoStocks[$row['entity_id']][] = $row['stock_id']; 
            }
        }
        foreach ($this as $creditmemo) {
            if (isset($creditmemoStocks[$creditmemo->getId()])) {
                $creditmemo->setData('stocks', $creditmemoStocks[$creditmemo->getId()]);
            }
        }
        return $this;
    }
    /**
     * Add stocks field
     * 
     * @return Innoexts_Warehouse_Model_Mysql4_Sales_Order_Grid_Collection
     */
    public function joinStocksField()
    {
        $this->addFilterToMap('stocks', 'creditmemo_stock.stock_id');
        $this->getSelect()->joinLeft(
            array('creditmemo_stock' => $this->getTable('warehouse/creditmemo_grid_warehouse')), 
            '(main_table.entity_id = creditmemo_stock.entity_id)', 
            array('stocks' => 'creditmemo_stock.warehouse_id')
        );
        return $this;
    }
}