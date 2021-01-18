<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/WirecardCheckoutSeamless.php';

/**
 * @see WirecardCheckoutSeamless_ORIGIN
 */
class wcs_ekonto_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 14;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::EKONTO;
	protected $_logoFilename     = 'ekonto.jpg';
}

MainFactory::load_origin_class('wcs_ekonto');
