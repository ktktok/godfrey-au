<?php class Infortis_Brands_Block_Brands extends Mage_Core_Block_Template
{
    public function getCurrentProductObject()
    {
        return Mage::registry('current_product');
    }

    public function getBrandAttributeId()
    {
        return Mage::helper('brands')->getCfg('general/attr_id');
    }

    public function getBrandAttributeTitle()
    {
        $x0b = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getBrandAttributeId());
        return $x0b->getStoreLabel();
    }

    public function getAllBrands()
    {
        $x0b = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getBrandAttributeId());
        $x0c = array();
        foreach ($x0b->getSource()->getAllOptions(false, true) as $x0d) {
            $x0c[] = $x0d['label'];
        }
        return $x0c;
    }

    public function getAllBrandsInUse()
    {
        $x0e = $this->getBrandAttributeId();
        $x0b = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $x0e);
        $x0f = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect($x0e)->addAttributeToFilter($x0e, array('neq' => ''))->addAttributeToFilter($x0e, array('notnull' => true));
        $x10 = array_unique($x0f->getColumnValues($x0e));
        return $x0b->getSource()->getOptionText(implode(',', $x10));
    }

    public function getBrand($x11)
    {
        $x12 = $x11->getResource()->getAttribute($this->getBrandAttributeId());
        return trim($x12->getFrontend()->getValue($x11));
    }

    public function getBrandImageDir()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'wysiwyg/infortis/brands/';
    }

    public function getBrandImageUrl($x13)
    {
        $x14 = trim(Mage::helper('brands')->getCfg('general/image_extension'));
        if (file_exists(Mage::getBaseDir('media') . '/wysiwyg/infortis/brands/' . str_replace("\040", "_", strtolower($x13)) . '.' . $x14)) {
            return $this->getBrandImageDir() . str_replace("\040", "_", strtolower($x13)) . '.' . $x14;
        }
        return Mage::getDesign()->getSkinBaseUrl() . '/images/noimg.png';
    }

    public function getBrandPageUrl($x13)
    {
        $x15 = '';
        $x16 = Mage::helper('brands');
        $x17 = $x16->getCfg('general/link_search_enabled');
        $x18 = trim($x16->getCfg('general/page_base_path'));
        if ($x17) {
            $x15 = Mage::getUrl() . 'catalogsearch/result/?q=' . str_replace("\040", "\x2b", $x13);
        } elseif ($x18 != '') {
            $x19 = ($x18 == '/') ? '' : $x18 . '/';
            $x15 = Mage::getUrl() . $x19 . str_replace(" ", "\x2d", strtolower($x13));
            if ($x16->getCfg('general/append_category_suffix')) $x15 .= Mage::getStoreConfig('catalog/seo/category_url_suffix');
        } else {
            $x15 = '';
        }
        return $x15;
    }
}