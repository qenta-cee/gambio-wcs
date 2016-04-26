<?php
/**
 * Shop System Plugins - Terms of use
 *
 * This terms of use regulates warranty and liability between Wirecard
 * Central Eastern Europe (subsequently referred to as WDCEE) and it's
 * contractual partners (subsequently referred to as customer or customers)
 * which are related to the use of plugins provided by WDCEE.
 *
 * The Plugin is provided by WDCEE free of charge for it's customers and
 * must be used for the purpose of WDCEE's payment platform integration
 * only. It explicitly is not part of the general contract between WDCEE
 * and it's customer. The plugin has successfully been tested under
 * specific circumstances which are defined as the shopsystem's standard
 * configuration (vendor's delivery state). The Customer is responsible for
 * testing the plugin's functionality before putting it into production
 * enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's
 * full functionality neither does WDCEE assume liability for any
 * disadvantage related to the use of this plugin. By installing the plugin
 * into the shopsystem the customer agrees to the terms of use. Please do
 * not use this plugin if you do not agree to the terms of use!
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