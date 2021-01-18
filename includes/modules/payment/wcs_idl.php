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
class wcs_idl_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 3;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::IDL;
	protected $_logoFilename     = 'ideal.jpg';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$field = '<select class="wcs_idl input-select form-control" name="wcs_financialinstitution_idl">';

		foreach(WirecardCEE_QMore_PaymentType::getFinancialInstitutions($this->_paymenttype) as $value => $name)
		{
			$field .= sprintf('<option value="%s">%s</option>', htmlspecialchars($value), $name);
		}

		$field .= '</select>';
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('financialinstitution'),
			'field' => $field
		);

		return $content;
	}


	public function pre_confirmation_check()
	{
		if(isset($_POST['wcs_financialinstitution_idl']))
		{
			$_SESSION['wcs_financialinstitution'] = $_POST['wcs_financialinstitution_idl'];
		}
	}
}

MainFactory::load_origin_class('wcs_idl');
