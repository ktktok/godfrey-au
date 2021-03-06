<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Godfreys_M2ePro_Model_Magento_Order extends Ess_M2ePro_Model_Magento_Order
{
    /** @var $quote Mage_Sales_Model_Quote */
    private $quote = NULL;

    /** @var $order Mage_Sales_Model_Order */
    private $order = NULL;

    private $additionalData = array();

    //########################################

    public function __construct(Mage_Sales_Model_Quote $quote)
    {
        $this->quote = $quote;
    }

    //########################################

    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    //########################################

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    //########################################

    public function buildOrder()
    {
        $this->createOrder();
    }

    private function createOrder()
    {
        try {
            $this->order = $this->placeOrder();
        } catch (Exception $e) {
            // Remove ordered items from customer cart
            // ---------------------------------------
            $this->quote->setIsActive(false)->save();
            // ---------------------------------------
            throw $e;
        }

        // Remove ordered items from customer cart
        // ---------------------------------------
        $this->quote->setIsActive(false)->save();
        // ---------------------------------------
    }

    private function placeOrder()
    {
        if (version_compare(Mage::helper('M2ePro/Magento')->getVersion(false), '1.4.1', '>=')) {
            // Multistore update & update shipping method
            $this->quote->reapplyStocks();
            $this->quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates()
                ->setLimitCarrier('m2eproshipping')->setShippingMethod('m2eproshipping_m2eproshipping');

            $this->quote->setTotalsCollectedFlag(false);
            $this->quote->collectTotals();

            $service = Mage::getModel('sales/service_quote', $this->quote);
            $service->setOrderData($this->additionalData);

            $service->submitAll();
            return $service->getOrder();
        }

        // Magento version 1.4.0 backward compatibility code

        /** @var $quoteConverter Mage_Sales_Model_Convert_Quote */
        $quoteConverter = Mage::getSingleton('sales/convert_quote');

        /** @var $orderObj Mage_Sales_Model_Order */
        $orderObj = $quoteConverter->addressToOrder($this->quote->getShippingAddress());

        $orderObj->setBillingAddress($quoteConverter->addressToOrderAddress($this->quote->getBillingAddress()));
        $orderObj->setShippingAddress($quoteConverter->addressToOrderAddress($this->quote->getShippingAddress()));
        $orderObj->setPayment($quoteConverter->paymentToOrderPayment($this->quote->getPayment()));

        $items = $this->quote->getShippingAddress()->getAllItems();

        foreach ($items as $item) {
            //@var $item Mage_Sales_Model_Quote_Item
            $orderItem = $quoteConverter->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($orderObj->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $orderObj->addItem($orderItem);
        }

        $orderObj->addData($this->additionalData);

        $orderObj->setCanShipPartiallyItem(false);
        $orderObj->place();
        $orderObj->save();

        return $orderObj;
    }

    //########################################
}