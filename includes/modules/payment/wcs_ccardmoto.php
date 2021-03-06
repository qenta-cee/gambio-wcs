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
require_once DIR_FS_DOCUMENT_ROOT . 'includes/modules/payment/wcs_ccard.php';

/**
 * @see WirecardCheckoutSeamless_ORIGIN
 */
class wcs_ccardmoto_ORIGIN extends wcs_ccard
{
	protected $_defaultSortOrder = 1;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::CCARD_MOTO;


	public function __construct()
	{
		parent::__construct();
		$c = strtoupper($this->code);
		define("MODULE_PAYMENT_{$c}_CUSTOMERGROUP_TITLE", $this->_seamless->getText('customergroup'));
		define("MODULE_PAYMENT_{$c}_CUSTOMERGROUP_DESC", $this->_seamless->getText('customergroup_desc'));
	}


	/**
	 * @return bool
	 */
	function _preCheck()
	{
		$c = strtoupper($this->code);

		return $_SESSION['customers_status']['customers_status_id']
		       == $this->constant("MODULE_PAYMENT_{$c}_CUSTOMERGROUP");
	}


	protected function _configuration()
	{
		$config = parent::_configuration();

		$config['CUSTOMERGROUP'] = array(
			'configuration_value' => '0', // default is admin
			'set_function'        => "wcs_cfg_pull_down_customergroups(",
			'use_function'        => "xtc_get_customers_status_name"
		);

		return $config;
	}

}

MainFactory::load_origin_class('wcs_ccardmoto');