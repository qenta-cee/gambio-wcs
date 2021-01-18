<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

class WirecardCheckoutSeamless_JSCheckoutExtenderComponent
	extends WirecardCheckoutSeamless_JSCheckoutExtenderComponent_parent
{
	function proceed()
	{
		parent::proceed();

		include_once(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE
		             . '/javascript/checkout/WirecardCheckoutSeamless.js');
	}
}
