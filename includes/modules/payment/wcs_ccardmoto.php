<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
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
