<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Observer_Product_AddUpdate_Abstract extends Ess_M2ePro_Model_Observer_Product_Abstract
{
    private $affectedListingsProducts = array();
    private $affectedOtherListings = array();

    //########################################

    /**
     * @return bool
     */
    public function canProcess()
    {
        return (string)$this->getEvent()->getProduct()->getSku() != '';
    }

    //########################################

    abstract protected function isAddingProductProcess();

    //########################################

    protected function areThereAffectedItems()
    {
        return count($this->getAffectedListingsProducts()) > 0 ||
               count($this->getAffectedOtherListings()) > 0;
    }

    // ---------------------------------------

    protected function getAffectedListingsProducts()
    {
        if (!empty($this->affectedListingsProducts)) {
            return $this->affectedListingsProducts;
        }

        return $this->affectedListingsProducts = Mage::getResourceModel('M2ePro/Listing_Product')
                                                            ->getItemsByProductId($this->getProductId());
    }

    protected function getAffectedOtherListings()
    {
        if (!empty($this->affectedOtherListings)) {
            return $this->affectedOtherListings;
        }

        return $this->affectedOtherListings = Mage::getResourceModel('M2ePro/Listing_Other')->getItemsByProductId(
            $this->getProductId(), array('component_mode' => Ess_M2ePro_Helper_Component_Ebay::NICK)
        );
    }

    //########################################
}