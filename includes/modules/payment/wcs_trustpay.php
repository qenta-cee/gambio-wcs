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
class wcs_trustpay_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder      = 26;
	protected $_paymenttype           = WirecardCEE_Stdlib_PaymentTypeAbstract::TRUSTPAY;
	protected $_financialInstitutions = null;
	protected $_logoFilename          = 'trustpay.jpg';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$field = '<select class="wcs_trustpay input-select" name="wcs_financialinstitution_trustpay">';

		foreach($this->_financialInstitutions as $fin)
		{
			$field .= sprintf('<option value="%s">%s</option>', htmlspecialchars($fin['id']), $fin['name']);
		}

		$field .= '</select>';
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('financialinstitution'),
			'field' => $field
		);

		return $content;
	}


	/**
	 * @return bool
	 */
	function _preCheck()
	{
		try
		{
			$this->_financialInstitutions = $this->_seamless->getFinancialInstitutions($this->_paymenttype);
			if($this->_financialInstitutions === false)
			{
				return false;
			}
		}
		catch(\Exception $e)
		{
			$this->_seamless->log(__METHOD__ . ':' . $e->getMessage(), LOG_WARNING);

			return false;
		}

		return true;
	}


	/**
	 * store financial institution in session
	 */
	public function pre_confirmation_check()
	{
		if(isset($_POST['wcs_financialinstitution_trustpay']))
		{
			$_SESSION['wcs_financialinstitution'] = $_POST['wcs_financialinstitution_trustpay'];
		}
	}
}

MainFactory::load_origin_class('wcs_trustpay');
