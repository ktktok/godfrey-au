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
 * Warehouse products tab
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Products 
    extends Innoexts_Core_Block_Adminhtml_Widget_Grid 
{
    /**
     * Retrieve warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }
    /**
     * Retrieve registered warehouse model
     *
     * @return Innoexts_Warehouse_Model_Warehouse
     */
    protected function getModel()
    {
        return Mage::registry('warehouse');
    }
    /**
     * Add filter
     * 
     * @param object $column
     * 
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Products
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }
    /**
     * Prepare collection object
     *
     * @return Varien_Data_Collection
     */
    protected function __prepareCollection()
    {
        $model = $this->getModel();
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('status');
        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
        $cond = array(
            '(({{table}}.use_config_manage_stock = 0) AND ({{table}}.manage_stock=1 AND {{table}}.is_in_stock=1))', 
            '(({{table}}.use_config_manage_stock = 0) AND ({{table}}.manage_stock=0))', 
        );
        if ($manageStock) {
            $cond[] = '(({{table}}.use_config_manage_stock = 1) AND ({{table}}.is_in_stock=1))';
        } else {
            $cond[] = '({{table}}.use_config_manage_stock = 1)';
        }
        $collection->joinField(
            'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', 
            '({{table}}.stock_id = \''.$model->getStockId().'\') AND ('.join(' OR ', $cond).')', 'inner'
        );
        return $collection;
    }
    /**
     * Add columns to grid
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Warehouse_Edit_Tab_Products
     */
    protected function _prepareColumns()
    {
        $helper = $this->getWarehouseHelper();
        $this->addColumn('entity_id', array(
            'header'        => $helper->__('ID'), 
            'width'         => '50px', 
            'type'          => 'number', 
            'index'         => 'entity_id', 
        ));
        $this->addColumn('name', array(
            'header'        => $helper->__('Name'),
            'index'         => 'name',
        ));
        $this->addColumn('type', array(
            'header'        => $helper->__('Type'), 
            'width'         => '60px', 
            'index'         => 'type_id', 
            'type'          => 'options', 
            'options'       => Mage::getSingleton('catalog/product_type')->getOptionArray(), 
        ));
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()->toOptionHash();
        $this->addColumn('set_name', array(
            'header'        => $helper->__('Attrib. Set Name'), 
            'width'         => '100px', 
            'index'         => 'attribute_set_id', 
            'type'          => 'options', 
            'options'       => $sets, 
        ));
        $this->addColumn('sku', array(
            'header'        => $helper->__('SKU'), 
            'width'         => '80px', 
            'index'         => 'sku', 
        ));
        $this->addColumn('price', array(
            'header'        => $helper->__('Price'), 
            'type'          => 'currency', 
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE), 
            'index'         => 'price', 
        ));
        $this->addColumn('qty', array(
            'header'        => $helper->__('Qty'), 
            'width'         => '100px', 
            'type'          => 'number', 
            'index'         => 'qty', 
        ));
        $this->addColumn('visibility', array(
            'header'        => $helper->__('Visibility'), 
            'width'         => 90, 
            'index'         => 'visibility', 
            'type'          => 'options', 
            'options'       => Mage::getSingleton('catalog/product_visibility')->getOptionArray(), 
        ));
        $this->addColumn('status', array(
            'header'        => $helper->__('Status'), 
            'width'         => 90, 
            'index'         => 'status', 
            'type'          => 'options', 
            'options'       => Mage::getSingleton('catalog/product_status')->getOptionArray(), 
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header'        => $helper->__('Websites'), 
                'width'         => '100px', 
                'sortable'      => false, 
                'index'         => 'websites', 
                'type'          => 'options', 
                'options'       => Mage::getModel('core/website')->getCollection()->toOptionHash(), 
            ));
        }
        parent::_prepareColumns();
        return $this;
    }
    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('grid_url') ? 
            $this->getData('grid_url') : 
            $this->getUrl('*/*/productsGrid', array('_current' => true));
    }
    /**
     * Get row URL
     * 
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getId()));
    }
}