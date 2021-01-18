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
class qcs_epay_bg_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 20;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::EPAYBG;
	protected $_logoFilename     = 'epay.png';
}

MainFactory::load_origin_class('qcs_epay_bg');
