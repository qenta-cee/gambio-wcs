<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/QentaCheckoutSeamless.php';
require_once DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/qcs_ccard.php';

/**
 * @see QentaCheckoutSeamless_ORIGIN
 */
class qcs_maestro_ORIGIN extends qcs_ccard
{
	protected $_defaultSortOrder = 1;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::MAESTRO;
	protected $_logoFilename     = 'maestro_secure_code.png';
}

MainFactory::load_origin_class('qcs_maestro');
