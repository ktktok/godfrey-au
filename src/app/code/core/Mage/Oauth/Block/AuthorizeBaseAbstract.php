<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * OAuth authorization block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Oauth_Block_AuthorizeBaseAbstract extends Mage_Oauth_Block_Authorize_Abstract
{
    /**
     * Retrieve user authorize form posting url
     *
     * @return string
     */
    abstract public function getPostActionUrl();

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    public function getRejectUrl()
    {
        $url = $this->getUrl($this->getRejectUrlPath() . ($this->getIsSimple() ? 'Simple' : ''),
            array('_query' => array('oauth_token' => $this->getToken())));
        return $url;
    }

    /**
     * Retrieve reject URL path
     *
     * @return string
     */
    abstract public function getRejectUrlPath();

    /**
     * Get form identity label
     *
     * @return string
     */
    abstract public function getIdentityLabel();

    /**
     * Get form identity label
     *
     * @return string
     */
    abstract public function getFormTitle();
}
