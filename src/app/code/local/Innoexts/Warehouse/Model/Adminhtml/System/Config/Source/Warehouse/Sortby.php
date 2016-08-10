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
 * Warehouse sort by source
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Adminhtml_System_Config_Source_Warehouse_Sortby 
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
     * Options getter
     * 
     * @return array
     */
    public function toOptionArray()
    {
        $helper = $this->getWarehouseHelper();
        return array(
            array(
                'value' => 'id', 
                'label' => $helper->__('ID')
            ), 
            array(
                'value' => 'code', 
                'label' => $helper->__('Code')
            ), 
            array(
                'value' => 'title', 
                'label' => $helper->__('Title')
            ), 
            array(
                'value' => 'priority', 
                'label' => $helper->__('Priority')
            ), 
            array(
                'value' => 'origin', 
                'label' => $helper->__('Origin')
            ), 
        );
    }
}