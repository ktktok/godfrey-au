<?php 
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Balance
 * @package    Feefocache
 * @copyright  Copyright (c) 2011 Balance
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Balance_Feefocache_IndexController extends Mage_Core_Controller_Front_Action
{
     public function indexAction()
     {
          $this->loadLayout();
          $this->renderLayout();
     }
      
}