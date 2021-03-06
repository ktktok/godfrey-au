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
 * One page checkout multiple mode shipping method
 *
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @author      Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Checkout_Onepage_Shipping_Method_Available_Singlemode 
    extends Mage_Checkout_Block_Onepage_Shipping_Method_Available 
{
    /**
     * Get shipping method
     * 
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->getAddressShippingMethod();
    }
    /**
     * Get shipping prices 
     */
    public function getShippingPrices()
    {
        $shippingPrices = array();
        foreach ($this->getShippingRates() as $carrierShippingRates) {
            foreach ($carrierShippingRates as $rate) {
                $shippingMethodCode = $rate->getCode();
                $price = (float) $rate->getPrice();
                $shippingPrices[$shippingMethodCode] = $price;
            }
        }
        return $shippingPrices;
    }
    /**
     * Get shipping prices JSON
     */
    public function getShippingPricesJSON()
    {
        return Mage::helper('core')->jsonEncode($this->getShippingPrices());
    }
    /**
     * Get current shipping price
     * 
     * @return float
     */
    public function getCurrentShippingPrice()
    {
        $price = null;
        $shippingMethod = $this->getShippingMethod();
        if ($shippingMethod) {
            foreach ($this->getShippingRates() as $carrierShippingRates) {
                foreach ($carrierShippingRates as $rate) {
                    $shippingMethodCode = $rate->getCode();
                    if ($shippingMethodCode == $shippingMethod) {
                        $price = (float) $rate->getPrice();
                        break 2;
                    }
                }
            }
        }
        return $price;
    }
    /**
     * Get current shipping price JS
     */
    public function getCurrentShippingPriceJS()
    {
        return Mage::helper('core')->jsonEncode($this->getCurrentShippingPrice());
    }
    /**
     * Get customer address stock distance string
     * 
     * @param int $stockId
     * 
     * @return string
     */
    public function getCustomerAddressStockDistanceString($stockId)
    {
        return $this->getWarehouseHelper()->getCustomerAddressStockDistanceString($stockId);
    }
}