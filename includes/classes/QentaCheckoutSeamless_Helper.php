<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

class QentaCheckoutSeamless_Helper
{
	public static function checkVersionBelow($max_version) {
		$coo_versioninfo = MainFactory::create_object('VersionInfo');
		$t_shop_versioninfo = $coo_versioninfo->get_shop_versioninfo();
		reset($t_shop_versioninfo);
		$version = filter_var(key($t_shop_versioninfo), FILTER_SANITIZE_NUMBER_INT);

		if (substr($version, 0, 2) < $max_version) {
			return true;
		} else {
			return false;
		}
	}
}
