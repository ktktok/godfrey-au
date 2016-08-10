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
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

var InnoWarehouseShippingMethod = Class.create(ShippingMethod, {

	validate: function() {
		var warehouseSelector = '.sp-warehouse-methods > dd';
		var methodSelector = '.shipping-method';
		var errors = new Array();
		var methods = null;
		Element.getElementsBySelector(this.form, warehouseSelector).each(function (warehouse) {
			var warehouseTitle = warehouse.readAttribute('title');
			methods = Element.getElementsBySelector(warehouse, methodSelector);
			if (methods.size() == 0) {
				errors.push(Translator.translate('There is no shipping methods available for %s warehouse.').sub('%s', warehouseTitle));
			}
		});
		if (errors.length == 0) {
			if(!this.validator.validate()) {
	            return false;
	        }
			Element.getElementsBySelector(this.form, warehouseSelector).each(function (warehouse) {
				var warehouseTitle = warehouse.readAttribute('title');
				var _checked = false;
				Element.getElementsBySelector(warehouse, methodSelector).each(function (el) {
					if (el.checked) {
						_checked = true;
					}
				});
				if (!_checked) {
					errors.push(Translator.translate('Please specify shipping method for %s warehouse.').sub('%s', warehouseTitle));
				}
			});
		}
		if (errors.length > 0) {
			alert(errors.join("\r\n"));
			return false;
		} else {
			return true;
		}
    }
});