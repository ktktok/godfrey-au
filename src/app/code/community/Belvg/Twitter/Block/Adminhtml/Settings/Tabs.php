<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Twitterconnect
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */?>
<?php

class Belvg_Twitter_Block_Adminhtml_Settings_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

   public function __construct()
    {        
        parent::__construct();
        $this->setId('design_tabs');
        $this->setDestElementId('design_edit_form');
        $this->setTitle(Mage::helper('core')->__('Twitter Settings'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('core')->__('General'),
            'content'   => $this->getLayout()->createBlock('twitter/adminhtml_settings_tab_general')->toHtml(),
        ));
	

        return parent::_prepareLayout();
    }
}