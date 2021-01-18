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
class qcs_giropay_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 4;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::GIROPAY;
	protected $_logoFilename     = 'giropay.png';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$content['fields'][] = array(
			'title' => $this->_seamless->getText('giropay_banknumber'),
			'field' => '<input type="text" class="qcs_giropay input-text" name="qcs_giropaybanknumber" data-qcs-fieldname="bankNumber" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('giropay_accountowner'),
			'field' => '<input type="text" class="qcs_giropay input-text" name="qcs_giropayaccountowner" data-qcs-fieldname="accountOwner" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('giropay_bankaccount'),
			'field' => '<input type="text" class="qcs_giropay input-text" name="qcs_giropaybankaccount" data-qcs-fieldname="bankAccount" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('qcs_giropay');
