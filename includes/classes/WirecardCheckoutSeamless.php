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

define('TABLE_PAYMENT_WCS', 'payment_wirecard_checkout_seamless');

require_once DIR_FS_DOCUMENT_ROOT . '/gm/classes/GMWirecardCheckoutSeamless.php';

class WirecardCheckoutSeamless_ORIGIN
{
	public    $code;
	public    $title;
	public    $description;
	public    $enabled;
	protected $_defaultSortOrder = 0;
	protected $_check;

	/**
	 * order is created before redirect to psp starts
	 * @var bool
	 */
	public $tmpOrders = true;

	/**
	 * displayed during checkout
	 * @var string
	 */
	protected $info;

	/**
	 * wirecard paymenttype
	 * @var string
	 */
	protected $_paymenttype = null;

	/**
	 * filename for paymenttype logo
	 * @var string
	 */
	protected $_logoFilename = null;

	/**
	 * @var WirecardCEE_QMore_DataStorage_Response_Initiation
	 */
	static protected $dataStore = null;

	/**
	 * whether datastorage init failed
	 * @var bool
	 */
	static protected $dataStoreFailed = false;

	/**
	 * List of available providers for invoice/installment
	 * @var array
	 */
	static public $invoiceinstallment_provider = array(
		array('id' => 'Payolution', 'text' => 'Payolution'),
		array('id' => 'RatePay', 'text' => 'RatePay')
	);

	/** @var GMWirecardCheckoutSeamless_ORIGIN */
	protected $_seamless;


	public function __construct()
	{
		$this->_seamless = MainFactory::create_object('GMWirecardCheckoutSeamless');

		$gpt        = str_replace('-', '', strtolower($this->_paymenttype));
		$this->code = sprintf('wcs_%s', $gpt);

		$c = strtoupper($this->code);

		$this->title = $this->constant("MODULE_PAYMENT_{$c}_TEXT_TITLE");

		$logoTag = ($this->_logoFilename) ? '<img src="' . DIR_WS_CATALOG . 'images/icons/wcs/' . $this->_logoFilename
		                                    . '" alt="'
		                                    . htmlspecialchars($this->constant("MODULE_PAYMENT_{$c}_TEXT_TITLE"))
		                                    . ' Logo" width="50px"/>&nbsp;&nbsp;' : '';

		$this->title       = $logoTag . $this->title;
		$this->info        = $this->constant("MODULE_PAYMENT_{$c}_TEXT_INFO"); // displayed in checkout
		$this->description = $this->constant("MODULE_PAYMENT_{$c}_TEXT_DESC"); // displayed in admin area
		$this->sort_order  = $this->constant("MODULE_PAYMENT_{$c}_SORT_ORDER");
		$this->description .= '<br /><br /><a href="' . GM_HTTP_SERVER . DIR_WS_ADMIN
		                      . 'wirecard_checkout_seamless_config.php" class="button" style="margin: auto auto 10px auto; ">'
		                      . $this->_seamless->getText('configure') . '</a>';

		$this->enabled = self::constant("MODULE_PAYMENT_{$c}_STATUS") == 'True';
		define("MODULE_PAYMENT_{$c}_STATUS_TITLE", $this->_seamless->getText('active'));
		define("MODULE_PAYMENT_{$c}_STATUS_DESC", '');
		define("MODULE_PAYMENT_{$c}_SORT_ORDER_TITLE", $this->_seamless->getText('sort_order'));
		define("MODULE_PAYMENT_{$c}_SORT_ORDER_DESC", '');
		define("MODULE_PAYMENT_{$c}_ALLOWED_TITLE", $this->_seamless->getText('allowed'));
		define("MODULE_PAYMENT_{$c}_ALLOWED_DESC", $this->_seamless->getText('allowed_desc'));
		define("MODULE_PAYMENT_{$c}_ZONE_TITLE", $this->_seamless->getText('zone'));
		define("MODULE_PAYMENT_{$c}_ZONE_DESC", $this->_seamless->getText('zone_desc'));
	}


	/**
	 * checkout for defined constant and return value
	 *
	 * @param $p_name
	 *
	 * @return mixed|null
	 */
	protected function constant($p_name)
	{
		return (defined($p_name)) ? constant($p_name) : null;
	}


	public function update_status()
	{
		global $order;
		$c    = strtoupper($this->code);
		$zone = (int)constant("MODULE_PAYMENT_{$c}_ZONE");

		if($this->enabled == true && $zone > 0)
		{
			$check_flag  = false;
			$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
			                            . $zone . "' and zone_country_id = '" . $order->billing['country']['id']
			                            . "' order by zone_id");

			while($check = xtc_db_fetch_array($check_query))
			{
				if($check['zone_id'] < 1)
				{
					$check_flag = true;
					break;
				}
				elseif($check['zone_id'] == $order->billing['zone_id'])
				{
					$check_flag = true;
					break;
				}
			}

			if($check_flag == false)
			{
				$this->enabled = false;
			}
		}
	}


	/**
	 * Return Text displayed besides the order button
	 *
	 * @return string
	 */
	public function process_button()
	{
	    $customer_id = function(){
            $configtype = gm_get_conf( 'WCS_configtype');
            switch($configtype){
                case 'test_no3d' : return 'D200411';
                case 'test_3d' : return 'D200411';
                case 'demo' : return 'D200001';
            }
            return gm_get_conf('WCS_CUSTOMER_ID');
        };
        $customer_id = $customer_id();

	    $consumerDeviceId = md5( $customer_id . "_" . microtime());
	    if(isset($_SESSION['wcs_consumer_device_id'])){
	        $consumerDeviceId = $_SESSION['wcs_consumer_device_id'];
        } else {
            $_SESSION['wcs_consumer_device_id'] = $consumerDeviceId;
        }

		return "<script language='JavaScript'>
                var di = {t:'" . $consumerDeviceId . "',v:'WDWL',l:'Checkout'};
              </script>
              <script type='text/javascript' src='//d.ratepay.com/" . $consumerDeviceId . "/di.js'></script>
              <noscript>
                <link rel='stylesheet' type='text/css' href='//d.ratepay.com/di.css?t=" . $consumerDeviceId . "&v=WDWL&l=Checkout'>
              </noscript>
              <object type='application/x-shockwave-flash' data='//d.ratepay.com/WDWL/c.swf' width='0' height='0'>
                <param name='movie' value='//d.ratepay.com/WDWL/c.swf' />
                <param name='flashvars' value='t=" . $consumerDeviceId . "&v=WDWL'/>
                <param name='AllowScriptAccess' value='always'/>
              </object>";
	}


	public function before_process()
	{
	}


	/**
	 * payment action, redirect to payment page
	 * @throws \Exception
	 */
	public function payment_action()
	{
		$orders_id = $GLOBALS['insert_id'];

		/** @var order_ORIGIN $order */
		$order = new order($orders_id);

		$init = $this->_seamless->initPayment($order, $this->_paymenttype);
		try
		{
			$initResponse = $init->initiate();
		}
		catch(Exception $e)
		{
			$this->_seamless->log(__METHOD__ . ':' . $e->getMessage(), LOG_WARNING);
			$_SESSION['gm_error_message'] = $this->_seamless->getText('datastorage_initerror');
			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}

		if($initResponse->getStatus() == WirecardCEE_QMore_Response_Initiation::STATE_FAILURE)
		{
			foreach($initResponse->getErrors() as $e)
			{
				$_SESSION['gm_error_message'] = html_entity_decode($e->getConsumerMessage());
			}

			xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
		}

		$q = sprintf("INSERT INTO %s (orders_id, paymentstate, paymenttype, request, created) VALUES (%d, '%s', '%s', '%s', NOW())",
		             TABLE_PAYMENT_WCS, $orders_id, 'INIT', $this->_paymenttype,
		             xtc_db_input(json_encode($init->getRequestData())));
		xtc_db_query($q);

        unset($_SESSION['wcs_consumer_device_id']);

		require 'checkout_wirecard_checkout_seamless.php';

		die;
	}


	public function after_process()
	{
	}


	/**
	 * display paymenttype on payment page
	 * @return array|bool
	 */
	public function selection()
	{
		if(!$this->_preCheck())
		{
			return false;
		}

		$paymentType = $this->_paymenttype;
		if($paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::MAESTRO
		   || $paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::CCARD_MOTO
		)
		{
			$paymentType = WirecardCEE_Stdlib_PaymentTypeAbstract::CCARD;
		}

		$fields   = array();
		$fields[] = array(
			'title' => '',
			'field' => sprintf('<input type="hidden" name="%s_type" value="%s"/>', $this->code,
			                   htmlspecialchars($paymentType))
		);

		if(self::$dataStore === null)
		{
			self::$dataStore = $this->initDataStorage();
			if(self::$dataStore !== null)
			{
				$fields[] = array(
					'title' => '',
					'field' => sprintf('<script type="text/javascript" src="%s"></script>',
					                   self::$dataStore->getJavascriptUrl())
				);

				$_SESSION['wcs_storage_id'] = self::$dataStore->getStorageId();
			}
			else
			{
				return false;
			}
		}

		return array('id' => $this->code, 'module' => $this->title, 'description' => $this->info, 'fields' => $fields);
	}


	public function javascript_validation()
	{
		return false;
	}


	public function pre_confirmation_check()
	{
		return false;
	}


	/**
	 * return additional info to be displayed on the checkout confirmation page
	 * does nothing, gambio bug?
	 *
	 * @return array
	 */
	public function confirmation()
	{
		return false;

		return array(
			'title'  => 'WCS',
			'fields' => array(
				array('title' => 'foo', 'field' => 'ssss')
			)
		);
	}


	public function get_error()
	{
		return false;
	}


	/**
	 * check whether paymenttype is available or not
	 * @return bool
	 */
	public function _preCheck()
	{
		if(self::$dataStoreFailed)
		{
			return false;
		}

		return true;
	}


	public function check()
	{
		if(!isset ($this->_check))
		{
			$c            = strtoupper($this->code);
			$check_query  = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION
			                             . " WHERE configuration_key='MODULE_PAYMENT_{$c}_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}

		return $this->_check;
	}


	/**
	 * initiate datastorage and store result in class var
	 * @return null|\WirecardCEE_QMore_DataStorage_Response_Initiation
	 */
	protected function initDataStorage()
	{
		if(self::$dataStoreFailed)
		{
			return null;
		}

		try
		{
			return $this->_seamless->initDataStorage();
		}
		catch(\Exception $e)
		{

			if(!isset($_SESSION['wcs_error_message']))
			{
				if($e instanceof GMWirecardCheckoutSeamlessUserException)
				{
					$_SESSION['wcs_error_message'] = $_SESSION['gm_error_message'] = $e->getMessage();
				}
				else
				{
					$_SESSION['wcs_error_message'] = $_SESSION['gm_error_message'] = $this->_seamless->getText('datastorage_initerror');
				}

				xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
			}
			else
			{
				unset($_SESSION['wcs_error_message']);
				self::$dataStoreFailed = true;
			}
		}

		return null;
	}


	/**
	 * plugin installation routine
	 */
	public function install()
	{
		$config     = $this->_configuration();
		$sort_order = 0;
		foreach($config as $key => $data)
		{
			$install_query = "INSERT INTO " . TABLE_CONFIGURATION
			                 . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) "
			                 . "VALUES ('MODULE_PAYMENT_" . strtoupper($this->code) . "_" . $key . "', '"
			                 . $data['configuration_value'] . "', '6', '" . $sort_order . "', '"
			                 . addslashes($data['set_function']) . "', '" . addslashes($data['use_function'])
			                 . "', NOW())";
			xtc_db_query($install_query);
			$sort_order++;
		}

		// create table for saving transaction data and logging
		$q = "CREATE TABLE IF NOT EXISTS " . TABLE_PAYMENT_WCS . "
          (id INT(11) NOT NULL AUTO_INCREMENT,
           orders_id INT(11) NOT NULL,
           paymenttype VARCHAR(32) NOT NULL,
           paymentstate VARCHAR(32) NOT NULL,
           ordernumber VARCHAR(64),
           request TEXT NOT NULL,
           response TEXT,
           message TEXT,
           created DATETIME NOT NULL,
           modified DATETIME,
           PRIMARY KEY (id))";
		xtc_db_query($q);
	}


	/**
	 * plugin removal routine
	 */
	public function remove()
	{
		xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '",
		                                                                                               $this->keys())
		             . "')");
	}


	/**
	 * return config keys
	 * @return array
	 */
	public function keys()
	{
		$ckeys = array_keys($this->_configuration());
		$keys  = array();
		foreach($ckeys as $k)
		{
			$keys[] = 'MODULE_PAYMENT_' . strtoupper($this->code) . '_' . $k;
		}

		return $keys;
	}


	/**
	 * Returns array of years for credit cards issue date
	 *
	 * @return array
	 */
	public function getCreditCardIssueYears()
	{
		return range(date('Y') - 10, date('Y'));
	}


	/**
	 * Returns array of years for credit cards expiry
	 *
	 * @return array
	 */
	public function getCreditCardYears()
	{
		return range(date('Y'), date('Y') + 10);
	}


	/**
	 * helper for sending order email
	 *
	 * @param $orders_id
	 */
	public function sendOrderEmail($p_orders_id)
	{
		$coo_send_order_process = MainFactory::create_object('SendOrderProcess');
		$coo_send_order_process->set_('order_id', $p_orders_id);
		$coo_send_order_process->proceed();
	}


	/**
	 * checks for invoice and installment, provider dependent
	 * @return bool
	 */
	protected function invoiceInstallmentPreCheck()
	{
		$c        = strtoupper($this->code);
		$provider = $this->constant("MODULE_PAYMENT_{$c}_PROVIDER");
		switch($provider)
		{
			case 'Payolution':
				return $this->payolutionPreCheck();

			case 'RatePay':
				return $this->ratePayPreCheck();

			default:
				return false;
		}
	}


	/**
	 * checks for payolution
	 * @return bool
	 */
	protected function payolutionPreCheck()
	{
		global $order, $xtPrice;

		$c          = strtoupper($this->code);

		$currency = $order->info['currency'];
		$total    = $order->info['total'];
		$amount   = round($xtPrice->xtcCalculateCurrEx($total, $currency), $xtPrice->get_decimal_places($currency));

        $currencies = explode(',', $this->constant("MODULE_PAYMENT_{$c}_CURRENCIES"));
        $currencies = array_map(function ($c)
        {
            return strtoupper(trim($c));
        }, $currencies);

		$maxAmount    = $this->constant("MODULE_PAYMENT_{$c}_MAX_AMOUNT");
		$country_code = $order->billing['country']['iso_code_2'];

        if ($this->constant("MODULE_PAYMENT_{$c}_EQUAL_ADDRESS") === "on" && $order->delivery !== $order->billing) {
            return false;
        }

		return (($amount >= $this->constant("MODULE_PAYMENT_{$c}_MIN_AMOUNT")
		            && (!strlen($maxAmount) || $amount <= $maxAmount))
		        && in_array($currency, $currencies)
		        && (in_array($country_code, Array('AT', 'DE', 'CH'))));
	}


	/**
	 * checks for RatePay
	 * @return bool
	 */
	protected function ratePayPreCheck()
	{
		global $order, $xtPrice;

		$c        = strtoupper($this->code);

		$currency = $order->info['currency'];
		$total    = $order->info['total'];
		$amount   = round($xtPrice->xtcCalculateCurrEx($total, $currency), $xtPrice->get_decimal_places($currency));

		$currencies = explode(',', $this->constant("MODULE_PAYMENT_{$c}_CURRENCIES"));
		$currencies = array_map(function ($c)
		{
			return strtoupper(trim($c));
		}, $currencies);

		$maxAmount = $this->constant("MODULE_PAYMENT_{$c}_MAX_AMOUNT");

        if ($this->constant("MODULE_PAYMENT_{$c}_EQUAL_ADDRESS") === "on" && $order->delivery !== $order->billing) {
            return false;
        }

		return (($amount >= $this->constant("MODULE_PAYMENT_{$c}_MIN_AMOUNT")
		         && (!strlen($maxAmount) || $amount <= $maxAmount))
		        && in_array($currency, $currencies));
	}


	/**
	 * configuration array
	 * @return array
	 */
	protected function _configuration()
	{
		$config = array(
			'STATUS'     => array(
				'configuration_value' => 'True',
				'set_function'        => 'gm_cfg_select_option(array(\'True\', \'False\'), ',
			),
			'ALLOWED'    => array(
				'configuration_value' => '',
			),
			'ZONE'       => array(
				'configuration_value' => '0',
				'use_function'        => 'xtc_get_zone_class_title',
				'set_function'        => 'xtc_cfg_pull_down_zone_classes(',
			),
			'SORT_ORDER' => array(
				'configuration_value' => $this->_defaultSortOrder,
			),
		);

		return $config;
	}
}


/**
 * invoice option list for module config
 *
 * @param string $p_provider_id
 * @param string $p_key
 *
 * @return string
 */
function wcs_cfg_pull_down_invoice_provider($p_provider_id, $p_key = '')
{
	$name = (($p_key) ? 'configuration[' . $p_key . ']' : 'configuration_value');

	$providers = WirecardCheckoutSeamless_ORIGIN::$invoiceinstallment_provider;

	return xtc_draw_pull_down_menu($name, $providers, $p_provider_id);
}

/**
 * checkbox for invoice config
 *
 * @param string $p_key
 * @return string
 */
function wcs_cfg_invoice_checkbox()
{
    $name = 'MODULE_PAYMENT_WCS_INVOICE_EQUAL_ADDRESS';
    $checked = (defined($name)) ? (constant($name) === 'on') ? 'on' : 'off' : 'off';
    $name = 'configuration[' . $name . ']';

    $values = array(
        array('id' => 'on', 'text' => 'On'),
        array('id' => 'off', 'text' => 'Off')
    );
    return xtc_draw_pull_down_menu($name, $values, $checked);
}

/**
 * checkbox for installment config
 *
 * @param string $p_key
 * @return string
 */
function wcs_cfg_installment_checkbox()
{
    $name = 'MODULE_PAYMENT_WCS_INSTALLMENT_EQUAL_ADDRESS';
    $checked = (defined($name)) ? (constant($name) === 'on') ? 'on' : 'off' : 'off';
    $name = 'configuration[' . $name . ']';

    $values = array(
        array('id' => 'on', 'text' => 'On'),
        array('id' => 'off', 'text' => 'Off')
    );
    return xtc_draw_pull_down_menu($name, $values, $checked);
}

/**
 * installment option list for module config
 *
 * @param string $provider_id
 * @param string $key
 *
 * @return string
 */
function wcs_cfg_pull_down_installment_provider($p_provider_id, $p_key = '')
{
	return wcs_cfg_pull_down_invoice_provider($p_provider_id, $p_key);
}

/**
 * option list for module config
 *
 * @param string $p_group_id
 * @param string $p_key
 *
 * @return string
 */
function wcs_cfg_pull_down_customergroups($p_group_id, $p_key = '')
{
	$name = (($p_key) ? 'configuration[' . $p_key . ']' : 'configuration_value');

	$customers_statuses_array = array(array());
	$customers_statuses_query = xtc_db_query("select customers_status_id, customers_status_name from "
	                                         . TABLE_CUSTOMERS_STATUS . " where language_id = '"
	                                         . $_SESSION['languages_id']
	                                         . "' AND customers_status_id != '1' order by customers_status_id");
	while($customers_statuses = xtc_db_fetch_array($customers_statuses_query))
	{
		$i                            = $customers_statuses['customers_status_id'];
		$customers_statuses_array[$i] = array(
			'id'   => $customers_statuses['customers_status_id'],
			'text' => $customers_statuses['customers_status_name']
		);
	}

	return xtc_draw_pull_down_menu($name, $customers_statuses_array, $p_group_id);
}

MainFactory::load_origin_class('WirecardCheckoutSeamless');