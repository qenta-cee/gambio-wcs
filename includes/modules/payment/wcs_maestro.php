<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/WirecardCheckoutSeamless.php';
require_once DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/wcs_ccard.php';

/**
 * @see WirecardCheckoutSeamless_ORIGIN
 */
class wcs_maestro_ORIGIN extends wcs_ccard
{
	protected $_defaultSortOrder = 1;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::MAESTRO;
	protected $_logoFilename     = 'maestro_secure_code.png';
}

MainFactory::load_origin_class('wcs_maestro');
