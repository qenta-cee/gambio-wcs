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
class qcs_eps_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 2;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::EPS;
	protected $_logoFilename     = 'eps.png';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$field = '<select class="qcs_eps input-select form-control" name="qcs_financialinstitution_eps">';

		foreach(QentaCEE\QMore\PaymentType::getFinancialInstitutions($this->_paymenttype) as $value => $name)
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
		if(isset($_POST['qcs_financialinstitution_eps']))
		{
			$_SESSION['qcs_financialinstitution'] = $_POST['qcs_financialinstitution_eps'];
		}
	}

}

MainFactory::load_origin_class('qcs_eps');
