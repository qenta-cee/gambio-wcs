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
class qcs_bancontact_mistercash_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 10;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::BMC;
	protected $_logoFilename     = 'bancontact.png';
}

MainFactory::load_origin_class('qcs_bancontact_mistercash');
