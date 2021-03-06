<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Data extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Retrieve the top link URL
	 *
	 * @return false|string
	 */
	public function getTopLinkUrl()
	{
		try {
			if ($this->isEnabled()) {
				if ($this->isFullyIntegrated()) {
					if ($this->_isCached('toplink_url')) {
						return $this->_cached('toplink_url');
					}
					
					$transport = new Varien_Object(array('toplink_url' => $this->getUrl()));
					
					Mage::dispatchEvent('wordpress_get_toplink_url', array('transport' => $transport));

					$this->_cache('toplink_url', $transport->getToplinkUrl());
					
					return $transport->getToplinkUrl();
				}

				return $this->getWpOption('home');
			}
		}
		catch (Exception $e) {
			$this->log('Magento & WordPress are not correctly integrated (see entry below).');
			$this->log($e->getMessage());
		}
		
		return false;
	}
	
	/**
	 * Retrieve the position for the top link
	 *
	 * @return false|int
	 */
	public function getTopLinkPosition()
	{
		if ($this->isEnabled()) {
			return (int)Mage::getStoreConfig('wordpress/toplink/position');
		}
		
		return false;
	}
	
	/**
	 * Returns the label for the top link
	 * This is also used for the breadcrumb
	 *
	 * @return false|string
	 */
	public function getTopLinkLabel()
	{
		if ($this->isEnabled()) {
			if ($this->_isCached('toplink_label')) {
				return $this->_cached('toplink_label');
			}
					
			$transport = new Varien_Object(array('toplink_label' => Mage::getStoreConfig('wordpress/toplink/label')));
			
			Mage::dispatchEvent('wordpress_get_toplink_label', array('transport' => $transport));

			$this->_cache('toplink_label', $transport->getToplinkLabel());
			
			return $transport->getToplinkLabel();
		}
		
		return false;
	}
	
	/**
	 * Returns the pretty version of the blog route
	 * This is deprecated. Instead, use self::getTopLinkLabel
	 *
	 * @return false|string
	 */
	public function getPrettyBlogRoute()
	{
		return $this->getTopLinkLabel();
	}
	
	/**
	  * Returns the URL Wordpress is installed on
	  *
	  * @param string $extra
	  * @return string
	  */
	public function getBaseUrl($extra = '')
	{
		return rtrim($this->getWpOption('siteurl'), '/') . '/' . $extra;
	}
	
	/**
	  * Get Wordpress Admin URL
	  *
	  */
	public function getAdminUrl($extra = null)
	{
		return $this->getBaseUrl('wp-admin/' . $extra);
	}
	
	/**
	 * Returns the given string prefixed with the Wordpress table prefix
	 *
	 * @return string
	 */
	public function getTableName($table)
	{
		return Mage::helper('wordpress/database')->getTableName($table);
	}
	
	/**
	 * Determine whether the module is enabled
	 * This can be changed by going to System > Configuration > Advanced
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('wordpress/module/enabled')
			&& !Mage::getStoreConfig('advanced/modules_disable_output/Fishpig_Wordpress');
	}
	
	/**
	  * Formats a Wordpress date string
	  *
	  */
	public function formatDate($date, $format = null, $f = false)
	{
		if ($format == null) {
			$format = $this->getDefaultDateFormat();
		}
		
		/**
		 * This allows you to translate month names rather than whole date strings
		 * eg. "March","Mars"
		 *
		 */
		$len = strlen($format);
		$out = '';
		
		for( $i = 0; $i < $len; $i++) {	
			$out .= $this->__(Mage::getModel('core/date')->date($format[$i], strtotime($date)));
		}
		
		return $out;
	}
	
	/**
	  * Formats a Wordpress date string
	  *
	  */
	public function formatTime($time, $format = null)
	{
		if ($format == null) {
			$format = $this->getDefaultTimeFormat();
		}
		
		return $this->formatDate($time, $format);
	}
	
	/**
	 * Split a date by spaces and translate
	 *
	 * @param string $date
	 * @param string $splitter = ' '
	 * @return string
	 */
	public function translateDate($date, $splitter = ' ')
	{
		$dates = explode($splitter, $date);
		
		foreach($dates as $it => $part) {
			$dates[$it] = $this->__($part);
		}
		
		return implode($splitter, $dates);
	}
	
	/**
	  * Return the default date formatting
	  *
	  */
	public function getDefaultDateFormat()
	{
		return $this->getWpOption('date_format', 'F jS, Y');
	}
	
	/**
	  * Return the default time formatting
	  *
	  */
	public function getDefaultTimeFormat()
	{
		return $this->getWpOption('time_format', 'g:ia');
	}
	
	/**
	 * Determine whether a WordPress plugin is enabled in the WP admin
	 *
	 * @param string $name
	 * @param bool $format
	 * @return bool
	 */
	public function isPluginEnabled($name)
	{
		$name = Mage::getSingleton('catalog/product_url')->formatUrlKey($name);
		$plugins = array();

		if ($plugins = $this->getWpOption('active_plugins')) {
			$plugins = unserialize($plugins);
		}
		
		if ($this->isWordPressMU() && Mage::helper('wpmultisite')->canRun()) {
			if ($networkPlugins = Mage::helper('wpmultisite')->getWpSiteOption('active_sitewide_plugins')) {
				$plugins += (array)unserialize($networkPlugins);
			}
		}

		if ($plugins) {
			foreach($plugins as $a => $b) {
				if (strpos($a . '-' . $b, $name) !== false) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Determine whether to force single store
	 *
	 * @return bool
	 */
	public function forceSingleStore()
	{
		return Mage::getStoreConfigFlag('wordpress/integration/force_single_store');
	}
	
	/**
	 * Determine whether Fishpig_WordpressMu can run
	 *
	 * @return bool
	 */
	public function isWordPressMU()
	{
		if (!$this->_isCached('is_wpmu')) {
			$this->_cache('is_wpmu', false);
			
			if ($this->isWordPressMUInstalled()) {
				$this->_cache('is_wpmu', Mage::helper('wpmultisite')->canRun());
			}
		}
		
		return $this->_cached('is_wpmu');
	}

	/**
	 * Determine whether Fishpig_WordpressMu is installed
	 *
	 * @return bool
	 */
	public function isWordPressMUInstalled()
	{
		if (!$this->_isCached('is_wpmu_installed')) {
			$config = Mage::getConfig();

			$isInstalled = (string)$config->getNode('modules/Fishpig_Wordpress_Addon_Multisite/active') === 'true'
				|| (string)$config->getNode('modules/Fishpig_WordpressMu/active') === 'true';
				
			$this->_cache('is_wpmu_installed', $isInstalled);
		}
		
		return $this->_cached('is_wpmu_installed');
	}
	
	/**
	 * Retrieve the upload URL
	 *
	 * @return string
	 */
	public function getFileUploadUrl()
	{
		$url = $this->getWpOption('fileupload_url');
		
		if (!$url) {
			foreach(array('upload_url_path', 'upload_path') as $config) {
				if ($value = $this->getWpOption($config)) {
					if (strpos($value, 'http') === false) {
						if (substr($value, 0, 1) !== '/') {
							$url = $this->getBaseUrl($value);
						}
					}
					else {
						$url = $value;
					}

					break;
				}
			}
			
			if (!$url) {
				if ($this->isWordPressMU() && !Mage::helper('wpmultisite')->isDefaultBlog() && Mage::helper('wpmultisite')->getBlogId()) {
					$url = $this->getBaseUrl('wp-content/uploads/sites/' . Mage::helper('wpmultisite')->getBlogId() . '/');
				}
				else {
					$url = $this->getBaseUrl('wp-content/uploads/');
				}
			}
		}
		
		return rtrim($url, '/') . '/';
	}
	
	/**
	 * Retrieve the local path to file cache path
	 *
	 * @return string
	 */
	public function getFileCachePath()
	{
		return Mage::getBaseDir('var') . DS . 'wordpress' . DS;
	}
	
	/**
	 * Retrieve the path for the WordPress installation
	 * Return false if path is invalid
	 *
	 * @return false|string
	 */
	public function getWordPressPath()
	{
		$path = $this->getRawWordPressPath();
		
		return is_dir($path) && is_file($path . 'wp-config.php')
			? $path
			: false;
	}
	
	/**
	 * Retrieve the path for the WordPress installation
	 * Do not check, just return
	 *
	 * @return string
	 */
	public function getRawWordPressPath()
	{
		$path = rtrim($this->getConfigValue('wordpress/integration/path'), DS);

		if ($path === '') {
			return false;
		}

		if (substr($path, 0, 1) !== DS) {
			return Mage::getBaseDir() . DS . $path . DS;
		}

		return rtrim($path, DS) . DS;
	}
	
	/**
	 * Determine whether an addon is installed
	 *
	 * @param string $addon
	 * @return bool
	 */
	public function isAddonInstalled($addon)
	{
		if (strpos($addon, '_') === false) {
			$addon = 'Fishpig_Wordpress_Addon_' . $addon;
		}

		return (string)Mage::getConfig()->getNode('modules/' . $addon . '/active') === 'true';
	}
	
	/**
	 * Apply Legacy Hacks
	 *
	 * @return $this
	 */
	public function applyLegacyHacks()
	{
		if (!Mage::registry('wordpress_legacy_hacks')) {
			Mage::register('wordpress_legacy_hacks', true);

			$version = Mage::getVersion();
	
			if (version_compare($version, '1.6.0.0', '<') && version_compare($version, '1.3.0.0', '>')) {
				require_once(Mage::getModuleDir('', 'Fishpig_Wordpress') . DS . 'legacy.php');
			}
		}
		
		return $this;
	}

    public function getShortDescription($string="")
    {
        $stringArray  = explode(' ',$string);
        $stringReturn= $string;
        $htmlElement = array("table","img","object");
        if (count($stringArray) >30) {
            $stringReturn = "";
            for ($i=0; $i<30; $i++) {
                foreach ($htmlElement as $e) {
                    if(strpos($stringArray[$i],$e) !== false)
                    return $this->closetags($stringReturn.'...');
                }
                $stringReturn .= $stringArray[$i].' ';
            }
            return $this->closetags($stringReturn.'...');
        }
        return $string;

    }

    public function closetags($html) {
        #put all opened tags into an array
        preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
        $openedtags = $result[1];

        #put all closed tags into an array
        preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
        $closedtags = $result[1];
        $len_opened = count( $openedtags );
        # all tags are closed
        if( count ( $closedtags ) == $len_opened ) {
            return $html;
        }
        $openedtags = array_reverse ( $openedtags );
        # close tags
        for( $i = 0; $i < $len_opened; $i++ ) {
            if ( !in_array ( $openedtags[$i], $closedtags ) ) {
                $html .= "</" . $openedtags[$i] . ">";
            }
            else {
                unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
            }
        }
        return $html;
    }
}
