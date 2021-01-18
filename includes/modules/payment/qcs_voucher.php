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
class qcs_voucher_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 25;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::VOUCHER;
	protected $_logoFilename     = 'voucher.png';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$content['fields'][] = array(
			'title' => $this->_seamless->getText('voucher_voucherid'),
			'field' => '<input type="text" class="qcs_voucher input-text" name="qcs_voucherid" data-qcs-fieldname="voucherId" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('qcs_voucher');
