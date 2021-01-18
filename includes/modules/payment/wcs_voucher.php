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
class wcs_voucher_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 25;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::VOUCHER;
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
			'field' => '<input type="text" class="wcs_voucher input-text" name="wcs_voucherid" data-wcs-fieldname="voucherId" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('wcs_voucher');