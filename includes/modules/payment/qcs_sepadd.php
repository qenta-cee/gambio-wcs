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
class qcs_sepadd_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 21;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::SEPADD;
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
			'field' => '<input type="text" class="qcs_sepadd input-text" name="qcs_sepaddiban" data-qcs-fieldname="bankAccountIban" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_bic'),
			'field' => '<input type="text" class="qcs_sepadd input-text qcs_sepaddbic" name="qcs_sepaddbic" data-qcs-fieldname="bankBic" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_accountowner'),
			'field' => '<input type="text" class="qcs_sepadd input-text" name="qcs_sepaddaccountowner" data-qcs-fieldname="accountOwner" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('qcs_sepadd');
