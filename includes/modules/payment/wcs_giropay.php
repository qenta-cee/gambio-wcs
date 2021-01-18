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
class wcs_giropay_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 4;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::GIROPAY;
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
			'field' => '<input type="text" class="wcs_giropay input-text" name="wcs_giropaybanknumber" data-wcs-fieldname="bankNumber" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('giropay_accountowner'),
			'field' => '<input type="text" class="wcs_giropay input-text" name="wcs_giropayaccountowner" data-wcs-fieldname="accountOwner" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('giropay_bankaccount'),
			'field' => '<input type="text" class="wcs_giropay input-text" name="wcs_giropaybankaccount" data-wcs-fieldname="bankAccount" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('wcs_giropay');
