<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Islider
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */
class AW_Islider_Block_Widget_Grid_Column_Renderer_Imagepreview extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        $_pattern = '<div style="text-align:center;">%s</div>';
        if($data) {
            $resized = false;
            $imageAddress = Mage::app()->getStore()->getConfig(Mage::helper('awislider')->isHttps() ? Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL : Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
            if($row->getData('type') == AW_Islider_Model_Source_Images_Type::FILE) {
                $resized = Mage::helper('awislider/files')->imageResize($data);
            } else {
                $resized =  Mage::helper('awislider/files')->imageResizeRemote($data);
            }
            $imageAddress .= Mage_Core_Model_Store::URL_TYPE_MEDIA.DS.Mage::helper('awislider/files')->getFolderName().DS.$resized;
        }
        return sprintf($_pattern, $resized ? ($data ? '<img src="'.$imageAddress.'" />' : $this->__('No Image')) : $this->__('N/A'));
    }
}
