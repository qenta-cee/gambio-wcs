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
class wcs_invoice_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 23;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::INVOICE;
	protected $_logoFilename     = 'invoice.png';


	public function __construct()
	{
		parent::__construct();
		$c = strtoupper($this->code);
		define("MODULE_PAYMENT_{$c}_MIN_AMOUNT_TITLE", $this->_seamless->getText('min_amount'));
		define("MODULE_PAYMENT_{$c}_MIN_AMOUNT_DESC", '');
		define("MODULE_PAYMENT_{$c}_MAX_AMOUNT_TITLE", $this->_seamless->getText('max_amount'));
		define("MODULE_PAYMENT_{$c}_MAX_AMOUNT_DESC", '');
		define("MODULE_PAYMENT_{$c}_PROVIDER_TITLE", $this->_seamless->getText('invoiceinstallment_provider'));
		define("MODULE_PAYMENT_{$c}_PROVIDER_DESC", '');
        define("MODULE_PAYMENT_{$c}_EQUAL_ADDRESS_TITLE", $this->_seamless->getText('equal_address'));
        define("MODULE_PAYMENT_{$c}_EQUAL_ADDRESS_DESC", '');
		define("MODULE_PAYMENT_{$c}_CURRENCIES_TITLE", $this->_seamless->getText('currencies'));
		define("MODULE_PAYMENT_{$c}_CURRENCIES_DESC", '');
	}


	/**
	 * @return bool
	 */
	function _preCheck()
	{
		return $this->invoiceInstallmentPreCheck();
	}


	/**
	 * module config
	 * @return mixed
	 */
	protected function _configuration()
	{
		$config = parent::_configuration();

		$config['PROVIDER']   = array(
			'configuration_value' => '',
			'set_function'        => "wcs_cfg_pull_down_invoice_provider( "
		);
		$config['CURRENCIES'] = array(
			'configuration_value' => 'EUR'
		);
        $config['EQUAL_ADDRESS'] = array(
            'configuration_value' => 'on',
            'set_function'        => "wcs_cfg_invoice_checkbox("
        );
		$config['MIN_AMOUNT'] = array(
			'configuration_value' => '10'
		);
		$config['MAX_AMOUNT'] = array(
			'configuration_value' => '3500'
		);

		return $config;
	}

}

MainFactory::load_origin_class('wcs_invoice');