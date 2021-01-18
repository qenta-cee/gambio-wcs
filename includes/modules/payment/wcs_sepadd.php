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
class wcs_sepadd_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 21;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::SEPADD;
	protected $_logoFilename     = 'sepa.png';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_iban'),
			'field' => '<input type="text" class="wcs_sepadd input-text" name="wcs_sepaddiban" data-wcs-fieldname="bankAccountIban" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_bic'),
			'field' => '<input type="text" class="wcs_sepadd input-text wcs_sepaddbic" name="wcs_sepaddbic" data-wcs-fieldname="bankBic" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_accountowner'),
			'field' => '<input type="text" class="wcs_sepadd input-text" name="wcs_sepaddaccountowner" data-wcs-fieldname="accountOwner" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('wcs_sepadd');
