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
 * Table rate methods controller
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Adminhtml_Tablerate_MethodController 
    extends Innoexts_Core_Controller_Adminhtml_Action 
{
    /**
     * Model names
     * 
     * @var array
     */
    protected $_modelNames = array(
        'shippingtablerate_method'  => 'shippingtablerate/tablerate_method', 
    );
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
     * Check is allowed action
     * 
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('sales/shipping/tablerates/methods');
    }
    /**
     * Index action
     */
    public function indexAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_indexAction('shippingtablerate_method', false, 'sales/shipping/tablerates/methods', array(
            $helper->__('Sales'), 
            $helper->__('Shipping'), 
            $helper->__('Table Rates'), 
            $helper->__('Manage Table Rate Methods'), 
        ));
    }
    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->_gridAction('shippingtablerate_method', true);
    }
    /**
     * New action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }
    /**
     * Edit action
     */
    public function editAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_editAction(
            'shippingtablerate_method', false, 'sales/shipping/tablerates/methods', 'method_id', '', 
            $helper->__('New Method'), $helper->__('Edit Method'), 
            array(
                $helper->__('Sales'), 
                $helper->__('Shipping'), 
                $helper->__('Table Rates'), 
                $helper->__('Manage Table Rate Methods'), 
            ), 
            $helper->__('This method no longer exists.')
        );
    }
    /**
     * Save action
     */
    public function saveAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_saveAction(
            'shippingtablerate_method', false, 'method_id', '', 'edit', 
            $helper->__('The method has been saved.'), 
            $helper->__('An error occurred while saving the method.')
        );
    }
    /**
     * Delete action
     */
    public function deleteAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_deleteAction(
            'shippingtablerate_method', false, 'method_id', '', 'edit', 
            $helper->__('Unable to find a method to delete.'), 
            $helper->__('The method has been deleted.')
        );
    }
    /**
     * Mass delete action
     */
    public function massDeleteAction()
    {
        $helper = $this->getWarehouseHelper();
        $this->_massDeleteAction(
            'shippingtablerate_method', false, 'method_id', '', 
            $helper->__('Please select method(s).'), 
            $helper->__('Total of %d record(s) have been deleted.')
        );
    }
}