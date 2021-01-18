<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/QentaCheckoutSeamless.php';

/**
 * @see QentaCheckoutSeamless_ORIGIN
 */
class qcs_skrillwallet_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 8;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::SKRILLWALLET;
	protected $_logoFilename     = 'skrill_digital_wallet.jpg';
}

MainFactory::load_origin_class('qcs_skrillwallet');
