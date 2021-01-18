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
class qcs_pbx_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 16;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::PBX;
	protected $_logoFilename     = 'paybox.jpg';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$content['fields'][] = array(
			'title' => $this->_seamless->getText('paybox_payernumber'),
			'field' => '<input type="text" class="qcs_pbx input-text" name="qcs_payboxpayernumber" data-qcs-fieldname="payerPayboxNumber" autocomplete="off" value=""/>'
		);

		return $content;
	}
}

MainFactory::load_origin_class('qcs_pbx');