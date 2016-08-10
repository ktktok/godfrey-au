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
 * Product stock priority renderer
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Stock_Priority_Renderer 
    extends Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Renderer_Abstract 
    implements Varien_Data_Form_Element_Renderer_Interface 
{
    /**
     * Constructor
     */
    public function __construct() {
        $this->setTemplate('warehouse/catalog/product/edit/tab/stock/priority/renderer.phtml');
    }
    /**
     * Sort values function
     *
     * @param mixed $a
     * @param mixed $b
     * 
     * @return int
     */
    protected function sortValues($a, $b)
    {
        if ($a['stock_id'] != $b['stock_id']) {
            return $a['stock_id'] < $b['stock_id'] ? -1 : 1;
        }
        return 0;
    }
    /**
     * Get values
     * 
     * @return array
     */
    public function getValues()
    {
        $helper     = $this->getWarehouseHelper();
        $values     = array();
        $stocksIds  = $helper->getCatalogInventoryHelper()->getStockIds();
        if (count($stocksIds)) {
            $readonly = $this->getElement()->getReadonly();
            $data = $this->getElement()->getValue();
            foreach ($stocksIds as $stockId) {
                $value = array('stock_id' => $stockId);
                if (isset($data[$stockId])) {
                    $value['priority'] = $data[$stockId];
                    $value['use_default'] = 0;
                } else {
                    $warehouse = $helper->getWarehouseByStockId($stockId);
                    if ($warehouse) {
                        $value['priority'] = $warehouse->getPriority();
                    } else {
                        $value['priority'] = 0;
                    }
                    $value['use_default'] = 1;
                }
                $value['readonly'] = $readonly;
                array_push($values, $value);
            }
        }
        usort($values, array($this, 'sortValues'));
        return $values;
    }
}