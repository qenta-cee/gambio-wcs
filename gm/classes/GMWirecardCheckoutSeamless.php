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

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . DIR_FS_DOCUMENT_ROOT . 'ext/wirecard/');

require_once DIR_FS_DOCUMENT_ROOT . '/ext/wirecard/Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace("WirecardCEE");

class GMWirecardCheckoutSeamless_ORIGIN
{
	/**
	 * @var string
	 */
	protected $_plugintype = 'gambio wcs';

	/**
	 * @var string
	 */
	protected $_pluginversion = '1.2.1';

	/**
	 * config parameters
	 * @var array
	 */
	public $_config = array(
		'basedata' => array(
			'configtype'  => array(
				'type'    => 'select',
				'options' => array(
					'prod'      => 'config_prod',
					'demo'      => 'config_demo',
					'test_no3d' => 'config_test_no3d',
					'test_3d'   => 'config_test_3d'
				)
			),
			'customer_id' => array(
				'type'     => 'text',
				'required' => true,
				'docref'   => 'https://integration.wirecard.at/doku.php/request_parameters#customerid',
				'trim'     => true,
				'default'  => 'D200001'
			),
			'shop_id'     => array(
				'type'     => 'text',
				'required' => false,
				'docref'   => 'https://integration.wirecard.at/doku.php/request_parameters#shopid',
				'trim'     => true,
				'default'  => 'seamless'
			),
			'secret'      => array(
				'type'     => 'text',
				'required' => true,
				'width'    => '270px',
				'docref'   => 'https://integration.wirecard.at/doku.php/security:start?s[]=secret#secret_and_fingerprint',
				'trim'     => true,
				'default'  => 'B8AKTPWBRMNBV455FG6M2DANE99WU2'
			),
			'backendpw'   => array(
				'type'     => 'text',
				'required' => true,
				'width'    => '200px',
				'docref'   => 'https://integration.wirecard.at/doku.php/back-end_operations:technical_wcs:start#password',
				'trim'     => true,
				'default'  => 'jcv45z'
			),
		),
		'options'  => array(
			'service_url'          => array(
				'type'      => 'text',
				'required'  => true,
				'validator' => 'url',
				'width'     => '270px',
				'docref'    => 'https://integration.wirecard.at/doku.php/request_parameters#serviceurl',
				'trim'      => true
			),
			'shop_name'            => array(
				'type'     => 'text',
				'required' => false,
				'docref'   => 'https://integration.wirecard.at/doku.php/request_parameters#customerstatement'
			),
			'send_additional_data' => array(
				'type'     => 'checkbox',
				'required' => false
			),
			'send_confirm_email'   => array(
				'type'     => 'checkbox',
				'required' => false,
				'docref'   => 'https://integration.wirecard.at/doku.php/request_parameters#confirmmail'
			),
			'auto_deposit'         => array(
				'type'     => 'checkbox',
				'required' => false,
				'docref'   => 'https://integration.wirecard.at/doku.php/request_parameters#autodeposit'
			),
			'send_basket'          => array(
				'type'     => 'checkbox',
				'required' => false,
				'docref'   => 'https://integration.wirecard.at/doku.php/request_parameters#shopping_basket_data'
			),
			'delete_cancel'  => array(
				'type'     => 'checkbox',
				'required' => false,
				'default'  => 1
			),
			'delete_failure'  => array(
				'type'     => 'checkbox',
				'required' => false,
				'default'  => 1
			),

		),
		'ccard'    => array(
			'pci3_dss_saq_a_enable'      => array(
				'type'     => 'checkbox',
				'required' => false,
				'docref'   => 'https://integration.wirecard.at/doku.php/wcs:pci3_fallback:start'
			),
			'iframe_css_url'             => array(
				'type'      => 'text',
				'width'     => '270px',
				'required'  => false,
				'validator' => 'url'
			),
			'creditcard_showcardholder'  => array(
				'type'     => 'checkbox',
				'required' => false,
				'default'  => 1
			),
			'creditcard_showcvc'         => array(
				'type'     => 'checkbox',
				'required' => false,
				'default'  => 1
			),
			'creditcard_showissuedate'   => array(
				'type'     => 'checkbox',
				'required' => false
			),
			'creditcard_showissuenumber' => array(
				'type'     => 'checkbox',
				'required' => false
			),
		),
		'order'    => array(
			'status_init'    => array(
				'type'     => 'order_status',
				'required' => false
			),
			'status_success' => array(
				'type'     => 'order_status',
				'required' => false
			),
			'status_pending' => array(
				'type'     => 'order_status',
				'required' => false
			),
			'status_cancel'  => array(
				'type'     => 'order_status',
				'required' => false
			),
			'status_error'   => array(
				'type'     => 'order_status',
				'required' => false
			)

		)
	);

	/**
	 * predefined test/demo accounts
	 * @var array
	 */
	protected $_presets = array(
		'demo'      => array(
			'customer_id' => 'D200001',
			'shop_id'     => 'seamless',
			'secret'      => 'B8AKTPWBRMNBV455FG6M2DANE99WU2',
			'backendpw'   => 'jcv45z'
		),
		'test_no3d' => array(
			'customer_id' => 'D200411',
			'shop_id'     => 'seamless',
			'secret'      => 'CHCSH7UGHVVX2P7EHDHSY4T2S4CGYK4QBE4M5YUUG2ND5BEZWNRZW5EJYVJQ',
			'backendpw'   => '2g4f9q2m'
		),
		'test_3d'   => array(
			'customer_id' => 'D200411',
			'shop_id'     => 'seamless3D',
			'secret'      => 'DP4TMTPQQWFJW34647RM798E9A5X7E8ATP462Z4VGZK53YEJ3JWXS98B9P4F',
			'backendpw'   => '2g4f9q2m'
		)
	);

	/** @var LogControl */
	protected $_logger;

	/** @var LanguageTextManager */
	protected $_txt;

	protected $_transferTypes = array(
		'EXISTINGORDER' => 'transfer_existingorder',
		'SKRILLWALLET'  => 'transfer_skrillwallet',
		'MONETA'        => 'transfer_moneta',
		'SEPA-CT'       => 'transfer_sepact'
	);

	const DB_KEY_PREFIX = 'WCS_';

	public function __construct()
	{
		if (class_exists('LogControl'))
		{
			$this->_logger = LogControl::get_instance();
		} else
		{
			$this->_logger = false;
		}

		$this->_txt = MainFactory::create_object('LanguageTextManager',
			array('wirecard_checkout_seamless', $_SESSION['languages_id']));
	}

	/**
	 * log messages
	 *
	 * @param     $message
	 * @param int $loglevel
	 */
	public function log($message, $loglevel = LOG_INFO)
	{
		if ($this->_logger === false)
		{
			return;
		}

		switch ($loglevel)
		{
		case LOG_ERR:
			$this->_logger->error($message, 'payment', 'payment.wcs');
			break;
		case LOG_WARNING:
			$this->_logger->warning($message, 'payment', 'payment.wcs');
			break;
		case LOG_INFO:
		case LOG_DEBUG:
		default:
			$this->_logger->notice($message, 'payment', 'payment.wcs');
			break;
		}
	}

	/**
	 * @param string $p_phraseName
	 *
	 * @return string
	 */
	public function getText($p_phraseName)
	{
		return $this->_txt->get_text($p_phraseName);
	}

	/**
	 * replace language placeholder prefixed with ## in content
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function replaceLanguagePlaceholders($p_content)
	{
		while (preg_match('/##(\w+)\b/', $p_content, $matches) == 1)
		{
			$replacement = $this->getText($matches[1]);
			if (!strlen($replacement))
			{
				$replacement = $matches[1];
			}
			$p_content = preg_replace('/##' . $matches[1] . '/', $replacement . '$1', $p_content, 1);
		}

		return $p_content;
	}

	/**
	 * return complete config incl. values set, optionally limit to one config param
	 *
	 * @param null $p_field
	 *
	 * @return array
	 */
	public function getConfig($p_field = null)
	{
		foreach ($this->_config as $group => &$fields)
		{
			foreach ($fields as $field => &$opts)
			{
				$db_key = self::DB_KEY_PREFIX . strtoupper($field);
				$dbvalue = gm_get_conf($db_key);
				if ($dbvalue != null)
				{
					$opts['value'] = $dbvalue;
				} else
				{
					if (array_key_exists('default', $opts))
					{
						$opts['value'] = $opts['default'];
					}
				}

				if ($p_field == $field)
				{
					return $opts;
				}
			}
		}

		return $this->_config;
	}

	/**
	 * read config value from db
	 *
	 * @param $p_key
	 *
	 * @return mixed
	 */
	public function getConfigValue($p_key)
	{
		$configtype = gm_get_conf(self::DB_KEY_PREFIX . 'configtype');
		if ($configtype != 'prod')
		{
			switch ($p_key)
			{
			case 'customer_id':
			case 'shop_id':
			case 'secret':
			case 'backendpw':
				return $this->_presets[$configtype][$p_key];
			}
		}

		return gm_get_conf(self::DB_KEY_PREFIX . strtoupper($p_key));
	}

	/**
	 * returns config preformated as string, used in support email
	 * @return string
	 */
	public function getConfigString()
	{
		$ret = '';
		$exclude = array('secret', 'backendpw');
		foreach ($this->_config as $group => &$fields)
		{
			foreach ($fields as $field => &$opts)
			{
				if (in_array($field, $exclude))
				{
					continue;
				}
				$db_key = self::DB_KEY_PREFIX . strtoupper($field);
				$dbvalue = gm_get_conf($db_key);
				if ($dbvalue != null)
				{
					$opts['value'] = $dbvalue;
				} else
				{
					if (array_key_exists('default', $opts))
					{
						$opts['value'] = $opts['default'];
					}
				}

				if (strlen($ret))
				{
					$ret .= "\n";
				}
				$ret .= sprintf("%s: %s", $this->getText($field), $opts['value']);
			}
		}

		return $ret;
	}

	/**
	 * save config value to db
	 *
	 * @param $p_key
	 * @param $p_value
	 */
	public function setConfigValue($p_key, $p_value)
	{
		$cfg = $this->getConfig($p_key);
		if (!is_array($cfg))
		{
			return;
		}

		if (array_key_exists('trim', $cfg) && $cfg['trim'])
		{
			$p_value = trim($p_value);
		}

		$db_key = self::DB_KEY_PREFIX . strtoupper($p_key);
		$p_value = xtc_db_input($p_value);
		gm_set_conf($db_key, $p_value);
	}

	/**
	 * validate config parameter value
	 *
	 * @param        $p_key
	 * @param        $p_value
	 * @param string $p_configtype
	 *
	 * @return bool
	 * @throws \GMWirecardCheckoutSeamlessException
	 */
	public function validateConfigValue($p_key, $p_value, $p_configtype = 'prod')
	{
		$cfg = $this->getConfig($p_key);
		if (!is_array($cfg))
		{
			throw new GMWirecardCheckoutSeamlessException('unknown parameter');
		}

		if ($p_configtype != 'prod')
		{
			switch ($p_key)
			{
			case 'customer_id':
			case 'shop_id':
			case 'secret':
			case 'backendpw':
				$p_value = $this->_presets[$p_configtype][$p_key];
			}
		}

		if (array_key_exists('trim', $cfg) && $cfg['trim'])
		{
			$p_value = trim($p_value);
		}

		if (!$cfg['required'] && !strlen($p_value))
		{
			return true;
		}

		if ($cfg['required'] && !strlen($p_value))
		{
			throw new GMWirecardCheckoutSeamlessException(sprintf($this->getText('param_required'),
				$this->getText($p_key)));
		}

		if (array_key_exists('validator', $cfg) && $cfg['validator'])
		{
			switch ($cfg['validator'])
			{
			case 'url':
				if (filter_var($p_value, FILTER_VALIDATE_URL) === false)
				{
					throw new GMWirecardCheckoutSeamlessException(sprintf($this->getText('param_invalid'),
						$this->getText($p_key)));
				}
				break;
			}
		}

		return true;
	}

	/**
	 * return available orders stati
	 * @return array
	 */
	public function getOrdersStati()
	{
		$query = "SELECT * FROM `orders_status` WHERE language_id = " . (int)$_SESSION['languages_id']
			. " ORDER BY orders_status_id ASC";
		$result = xtc_db_query($query);
		$stati = array();
		while (($row = xtc_db_fetch_array($result)))
		{
			$stati[$row['orders_status_id']] = $row['orders_status_name'];
		}

		return $stati;
	}

	/**
	 * make a quick test call (datastorage init), just to check whether credentials are ok
	 * @return bool
	 * @throws \GMWirecardCheckoutSeamlessException
	 */
	public function testConfig()
	{
		$dataStorageInit = new WirecardCEE_QMore_DataStorageClient($this->_getConfigArray());
		$dataStorageInit->setReturnUrl(xtc_href_link('callback/wirecard/checkout_seamless_datastorage_return.php', '',
			'SSL'));
		$dataStorageInit->setOrderIdent(session_id());

		try
		{
			$response = $dataStorageInit->initiate();
			if ($response->getStatus() != WirecardCEE_QMore_DataStorage_Response_Initiation::STATE_SUCCESS)
			{
				$msg = array();
				foreach ($response->getErrors() as $error)
				{
					$msg[] = $error->getConsumerMessage();
				}
				throw new GMWirecardCheckoutSeamlessException(implode('<br/>', $msg));
			}
		} catch (Exception $e)
		{
			throw new GMWirecardCheckoutSeamlessException($e->getMessage());
		}

		return true;
	}

	/**
	 * init wirecard datastorage
	 * @return null|\WirecardCEE_QMore_DataStorage_Response_Initiation
	 * @throws \GMWirecardCheckoutSeamlessException
	 */
	public function initDataStorage()
	{
		$dataStorageInit = new WirecardCEE_QMore_DataStorageClient($this->_getConfigArray());
		$dataStorageInit->setReturnUrl(xtc_href_link('callback/wirecard/checkout_seamless_datastorage_return.php', '',
			'SSL'));
		$dataStorageInit->setOrderIdent(session_id());
		$response = null;
		if ($this->getConfigValue('pci3_dss_saq_a_enable'))
		{
			$dataStorageInit->setJavascriptScriptVersion('pci3');

			if (strlen(trim($this->getConfigValue('iframe_css_url'))))
			{
				$dataStorageInit->setIframeCssUrl(trim($this->getConfigValue('iframe_css_url')));
			}

			$dataStorageInit->setCreditCardShowCardholderNameField($this->getConfigValue('creditcard_showcardholder'));
			$dataStorageInit->setCreditCardShowCvcField($this->getConfigValue('creditcard_showcvc'));
			$dataStorageInit->setCreditCardShowIssueDateField($this->getConfigValue('creditcard_showissuedate'));
			$dataStorageInit->setCreditCardShowIssueNumberField($this->getConfigValue('creditcard_showissuenumber'));
		}

		try
		{
			$response = $dataStorageInit->initiate();
			if ($response->getStatus() != WirecardCEE_QMore_DataStorage_Response_Initiation::STATE_SUCCESS)
			{
				$dsErrors = $response->getErrors();
				$msg = array();
				foreach ($dsErrors as $error)
				{
					$msg[] = 'DataStorage: ' . $error->getConsumerMessage();
				}

				$msg = implode(', ', $msg);
				$this->log(__METHOD__ . ':' . $msg);
				throw new GMWirecardCheckoutSeamlessException($msg);
			}
		} catch (Exception $e)
		{
			$this->log(__METHOD__ . ':' . $e->getMessage());
			throw new GMWirecardCheckoutSeamlessException($e->getMessage());
		}

		return $response;
	}

	/**
	 * read from datastorage
	 *
	 * @param $storageId
	 *
	 * @return \WirecardCEE_QMore_DataStorage_Response_Read
	 */
	public function readDataStorage($storageId)
	{
		$dataStorageRead = new WirecardCEE_QMore_DataStorage_Request_Read($this->_getConfigArray());

		$readResponse = $dataStorageRead->read($storageId);

		return $readResponse;
	}

	/**
	 * @param order_ORIGIN $order
	 * @param string $paymentType
	 *
	 * @return \WirecardCEE_QMore_FrontendClient|\WirecardCEE_QMore_Response_Initiation
	 * @throws \Exception
	 */
	public function initPayment($order, $paymentType)
	{
		/** @var xtcPrice_ORIGIN $xtPrice */
		global $xtPrice;

		$init = new WirecardCEE_QMore_FrontendClient($this->_getConfigArray());

		$init->setPluginVersion($this->_getPluginVersion());

		$init->setOrderReference(sprintf('%010d', $order->info['orders_id']));

		if ($this->getConfigValue('send_confirm_email'))
		{
			$init->setConfirmMail(STORE_OWNER_EMAIL_ADDRESS);
		}

		if ($this->getConfigValue('auto_deposit'))
		{
			$init->setAutoDeposit(true);
		}

		$init->setStorageReference(session_id(), $_SESSION['wcs_storage_id']);

		if ($paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::EPS
			|| $paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::IDL
			|| $paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::TRUSTPAY
		)
		{
			$init->setFinancialInstitution($_SESSION['wcs_financialinstitution']);
		}

		// add order id as param, for fallback if cart modifications (fraud attempt) has been made
		// tmp_oOID will be clean'd if cart has been openend.
		$returnUrl = xtc_href_link('callback/wirecard/checkout_seamless_return.php',
			'fboOID=' . $order->info['orders_id'], 'SSL', false);


        $confirmUrl = xtc_href_link('callback/wirecard/checkout_seamless_confirm.php', '', 'SSL', false);

		$total = isset($order->info['pp_total']) ? $order->info['pp_total'] : $total = $order->info['total'];
		$decimalPlaces = $xtPrice->get_decimal_places($order->info['currency']);
        $customerMail = (string) $order->customer['email_address'];
		$init->setAmount(number_format($total, $decimalPlaces, '.', ''))
			->setCurrency($order->info['currency'])
			->setPaymentType($paymentType)
			->setOrderDescription($this->getUserDescription($order))
			->setSuccessUrl($returnUrl)
			->setPendingUrl($returnUrl)
			->setCancelUrl($returnUrl)
			->setFailureUrl($returnUrl)
            ->setConfirmUrl($confirmUrl)
			->setServiceUrl($this->getConfigValue('service_url'))
            ->createConsumerMerchantCrmId($customerMail)
			->setConsumerData($this->getConsumerData($order, $paymentType));

        if (isset($_SESSION['wcs_consumer_device_id'])) {
            $init->consumerDeviceId = $_SESSION['wcs_consumer_device_id'];
        }

		$init->orders_id = $order->info['orders_id'];

		if ($this->getConfigValue('send_basket')
			|| ($paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::INVOICE
				&& MODULE_PAYMENT_INVOICE_PROVIDER == 'RatePay')
			|| ($paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::INSTALLMENT
				&& MODULE_PAYMENT_INVOICE_PROVIDER == 'RatePay')
		)
		{
			$basket = new WirecardCEE_Stdlib_Basket();
			$basket->setCurrency($order->info['currency']);

			foreach ($order->products as $idx => $p)
			{
				$price = $xtPrice->xtcRemoveTax($p['price'], $p['tax']);
				$item = new WirecardCEE_Stdlib_Basket_Item();
				$item->setUnitPrice(number_format($price, $decimalPlaces, '.', ''));
				$item->setDescription($p['name']);
				$item->setArticleNumber($p['model']);
				$vat = $p['final_price'] - round($price, 2) * (int)$p['qty'];
				$item->setTax(number_format($vat, $decimalPlaces, '.', ''));
				$basket->addItem($item, (int)$p['qty']);
			}

			$item = new WirecardCEE_Stdlib_Basket_Item();
			$item->setArticleNumber('shipping');
			$item->setUnitPrice(number_format($order->info['pp_shipping'], $decimalPlaces, '.', ''));
			$item->setTax(0);
			$item->setDescription($order->info['shipping_method']);
			$basket->addItem($item);

			foreach ($basket->__toArray() as $k => $v)
			{
				$init->$k = $v;
			}
		}

		$init->generateCustomerStatement($this->getConfigValue('shop_name'));

		$this->log(__METHOD__ . ':' . print_r($init->getRequestData(), true));

		return $init;
	}

	/**
	 * handle server2server request, no session available
	 *
	 * @param $postdata
	 *
	 * @return string
	 */
	public function handleConfirm($postdata)
	{
		$this->log(__METHOD__ . ':' . print_r($postdata, true));

		$return = WirecardCEE_QMore_ReturnFactory::getInstance($postdata, $this->getConfigValue('secret'));

		if (!$return->validate())
		{
			$this->log(__METHOD__ . ':Validation error: invalid response', LOG_WARNING);

			return WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString('Validation error: invalid response');
		}

		$orders_id = (int)$postdata['orders_id'];

		$order_status = $this->_getOrdersStatus($orders_id);
		if($order_status !== null)	{
            if ($order_status == $this->getConfigValue('status_success') || $order_status == $this->getConfigValue('status_error')) {
				$this->log(__METHOD__ . 'Order Workflow manipulation', LOG_WARNING);

				return WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString('Validation error: invalid response');
			}
		} else {
			$this->log(__METHOD__ . 'Validation error: invalid response', LOG_WARNING);

			return WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString('Validation error: invalid response');
		}

		try
		{
			$ordernumber = null;
			xtc_db_query(sprintf("UPDATE %s SET response='%s', paymentstate='%s', modified = NOW() WHERE orders_id = %d",
				TABLE_PAYMENT_WCS, xtc_db_input(json_encode($postdata)), $return->getPaymentState(),
				$orders_id));

			switch ($return->getPaymentState())
			{
			case WirecardCEE_QMore_ReturnFactory::STATE_SUCCESS:
				/** @var WirecardCEE_QMore_Return_Success $return */
				$ordernumber = $return->getOrderNumber();
				$this->updateOrdersStatus($orders_id, $this->getConfigValue('status_success'));
				break;

			case WirecardCEE_QMore_ReturnFactory::STATE_PENDING:
				$ordernumber = $return->getOrderNumber();
				/** @var WirecardCEE_QMore_Return_Pending $return */
				$this->updateOrdersStatus($orders_id, $this->getConfigValue('status_pending'));
				break;

			case WirecardCEE_QMore_ReturnFactory::STATE_CANCEL:
				$this->_removeOrder($orders_id, true, !$this->getConfigValue('delete_cancel'));
				$this->updateOrdersStatus($orders_id, $this->getConfigValue('status_cancel'));
				break;

			case WirecardCEE_QMore_ReturnFactory::STATE_FAILURE:
				$this->_removeOrder($orders_id, true, !$this->getConfigValue('delete_failure'));
				$this->updateOrdersStatus($orders_id, $this->getConfigValue('status_error'));
				$msg = '';
				for ($i = 1; $i <= $return->getNumberOfErrors(); $i++)
				{
					$key = sprintf('error_%d_consumerMessage', $i);
					if (strlen($msg))
					{
						$msg .= "<br/>";
					}
					$msg .= iconv('ISO-8859-1', 'UTF-8', $postdata[$key]);
				}
				xtc_db_query(sprintf("UPDATE %s SET message='%s' WHERE orders_id = %d", TABLE_PAYMENT_WCS,
					xtc_db_input($msg), $orders_id));
			default:
			}
			if ($ordernumber !== null)
			{
				xtc_db_query(sprintf("UPDATE %s SET ordernumber='%s' WHERE orders_id = %d", TABLE_PAYMENT_WCS,
					$ordernumber, $orders_id));
			}
		} catch (Exception $e)
		{
			$this->log(__METHOD__ . ':' . $e->getMessage(), LOG_WARNING);

			return WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString($e->getMessage());
		}

		return WirecardCEE_QMore_ReturnFactory::generateConfirmResponseString();
	}

	/**
	 * gets order status
	 *
	 * @param int $orders_id
	 * @return null|string
	 */
	protected function _getOrdersStatus($orders_id)
	{
		$order = array();
		$q = xtc_db_query('SELECT orders_status FROM ' . TABLE_ORDERS . ' WHERE orders_id = "'.$orders_id.'" LIMIT 1;');
		if(xtc_db_num_rows($q))	{
			$order = xtc_db_fetch_array($q);
		}

		if (empty($order)) {
			return null;
		}

		return $order['orders_status'];
	}

	/**
	 * update gx order, set status and comment
	 *
	 * @param        $p_orders_id
	 * @param        $p_status_id
	 * @param string $p_comment
	 */
	public function updateOrdersStatus($p_orders_id, $p_status_id, $p_comment = '')
	{
		xtc_db_query(sprintf("UPDATE `orders` SET `orders_status` = %d, `last_modified` = NOW() WHERE orders_id = %d",
			$p_status_id, $p_orders_id));
		xtc_db_query(sprintf("INSERT INTO `orders_status_history` SET `orders_id` = %d, `orders_status_id` = %d, `date_added` = NOW(), `customer_notified` = 0, `comments` = '%s'",
			$p_orders_id, $p_status_id, xtc_db_input($p_comment)));
	}

	/**
	 * return plugin version
	 * @return string
	 */
	public function getVersion()
	{
		return $this->_plugintype . ' ' . $this->_pluginversion;
	}

	/**
	 * Start of backend operations
	 */

	/**
	 * return financial institutions from wirecard (backend operation)
	 *
	 * @param string $paymentType
	 *
	 * @return bool
	 */
	public function getFinancialInstitutions($paymentType)
	{
		$cl = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		$response = $cl->getFinancialInstitutions($paymentType);
		if (!$response->hasFailed())
		{
			$ret = $response->getFinancialInstitutions();
			$c = null;
			if (class_exists('Collator'))
			{
				$c = new Collator('root');
			}

			uasort($ret, function ($a, $b) use ($c)
			{
				if ($c === null)
				{
					return strcmp($a['id'], $b['id']);
				} else
				{
					return $c->compare($a['name'], $b['name']);
				}
			});

			return $ret;
		} else
		{
			$this->log(__METHOD__ . ':' . print_r($response->getErrors(), true), LOG_WARNING);

			return false;
		}
	}

	/**
	 * read order details from wirecard
	 *
	 * @param $p_ordernumber
	 *
	 * @return \WirecardCEE_QMore_Response_Backend_GetOrderDetails
	 */
	public function getOrderDetails($p_ordernumber)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->getOrderDetails($p_ordernumber);
	}

	/**
	 * deposit amount
	 *
	 * @param $p_ordernumber
	 * @param $p_amount
	 * @param $p_currency
	 *
	 * @return \WirecardCEE_QMore_Response_Backend_Deposit
	 */
	public function deposit($p_ordernumber, $p_amount, $p_currency)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->deposit($p_ordernumber, $p_amount, $p_currency);
	}

	/**
	 * make a deposit reversal
	 *
	 * @param $p_ordernumber
	 * @param $p_paymentnumber
	 *
	 * @return \WirecardCEE_QMore_Response_Backend_DepositReversal
	 */
	public function depositReversal($p_ordernumber, $p_paymentnumber)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->depositReversal($p_ordernumber, $p_paymentnumber);
	}

	/**
	 * revoke approval
	 *
	 * @param $p_ordernumber
	 *
	 * @return \WirecardCEE_QMore_Response_Backend_ApproveReversal
	 */
	public function approveReversal($p_ordernumber)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->approveReversal($p_ordernumber);
	}

	/**
	 * refund a certain amount
	 *
	 * @param $p_ordernumber
	 * @param $p_amount
	 * @param $p_currency
	 *
	 * @return \WirecardCEE_QMore_Response_Backend_Refund
	 */
	public function refund($p_ordernumber, $p_amount, $p_currency)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->refund($p_ordernumber, $p_amount, $p_currency);
	}

	/**
	 * revert refund
	 *
	 * @param $p_ordernumber
	 * @param $p_creditnumber
	 *
	 * @return \WirecardCEE_QMore_Response_Backend_RefundReversal
	 */
	public function refundReversal($p_ordernumber, $p_creditnumber)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->refundReversal($p_ordernumber, $p_creditnumber);
	}

	/**
	 * transfer funds
	 *
	 * @param $p_transfer_type
	 *
	 * @return \WirecardCEE_QMore_Request_Backend_TransferFund
	 * @throws \WirecardCEE_Stdlib_Exception_InvalidTypeException
	 */
	public function transferFund($p_transfer_type)
	{
		$client = new WirecardCEE_QMore_BackendClient($this->_getBackendConfigArray());

		return $client->transferFund($p_transfer_type);
	}


	/**
	 * End of backend operations
	 */

	/**
	 * read transaction data from database
	 *
	 * @param $p_orders_id
	 *
	 * @return array|bool|mixed
	 */
	public function getTransactioData($p_orders_id)
	{
		$t_query = "SELECT * FROM payment_wirecard_checkout_seamless WHERE orders_id = ':orders_id'";
		$t_query = strtr($t_query, array(':orders_id' => (int)$p_orders_id));
		$t_result = xtc_db_query($t_query);

		return $t_row = xtc_db_fetch_array($t_result);
	}

	/**
	 * return available fund transfer types
	 * @return array
	 */
	public function getTransferTypes()
	{
		$ret = array();
		foreach ($this->_transferTypes as $t => $lbl)
		{
			$ret[$t] = $this->getText($lbl);
		}

		return $ret;
	}

	/**
	 * read available currencies from db
	 * @return array
	 */
	public function getCurrencies()
	{
		$currencies = array();
		$res = xtc_db_query("SELECT * FROM " . TABLE_CURRENCIES);
		while (($cur = xtc_db_fetch_array($res)))
		{
			$currencies[$cur['code']] = $cur['title'];
		}

		return $currencies;
	}

	/**
	 * return config array to be used for client lib
	 * @return array
	 */
	protected function _getConfigArray()
	{
		return Array(
			'CUSTOMER_ID' => $this->getConfigValue('customer_id'),
			'SHOP_ID'     => $this->getConfigValue('shop_id'),
			'SECRET'      => $this->getConfigValue('secret'),
			'LANGUAGE'    => $_SESSION['language_code'],
		);
	}

	/**
	 * return config array to be used for client lib, backend ops
	 * @return array
	 */
	protected function _getBackendConfigArray()
	{
		$cfg = $this->_getConfigArray();
		$cfg['PASSWORD'] = $this->getConfigValue('backendpw');

		return $cfg;
	}

	/**
	 * return plugin version
	 * @return string
	 */
	protected function _getPluginVersion()
	{
		return WirecardCEE_QMore_FrontendClient::generatePluginVersion('Gambio', $this->_getGxVersion(),
			$this->_plugintype, $this->_pluginversion);
	}

	/**
	 * fetch zonecode from db
	 *
	 * @param string $p_zonename
	 *
	 * @return null|string
	 */
	protected function _getZoneCodeByName($p_zonename)
	{
		$sql = 'SELECT zone_code FROM ' . TABLE_ZONES . ' WHERE zone_name=\'' . xtc_db_input($p_zonename)
			. '\' LIMIT 1;';
		$result = xtc_db_query($sql);
		$resultRow = xtc_db_fetch_array($result);
		if ($resultRow === false)
		{
			return null;
		}

		return $resultRow[0];
	}

	/**
	 * fetch additional customer data from db
	 *
	 * @param $p_customer_id
	 *
	 * @return array|bool|mixed
	 */
	protected function _getCustomerData($p_customer_id)
	{
		$sql = 'SELECT customers_dob, customers_fax, customers_vat_id FROM ' . TABLE_CUSTOMERS
			. ' WHERE customers_id="' . (int)$p_customer_id . '" LIMIT 1;';
		$result = xtc_db_query($sql);

		return xtc_db_fetch_array($result);
	}

	/**
	 * helper for getting gambio version
	 * @return mixed
	 */
	protected function _getGxVersion()
	{
		require(DIR_FS_CATALOG . 'release_info.php');

		/** @global string $gx_version */

		return $gx_version;
	}

	/**
	 * Returns desription of customer - will be displayed in Wirecard backend
	 * @return string
	 */
	protected function getUserDescription($order)
	{
		return sprintf('%s %s %s', $order->customer['email_address'], $order->customer['firstname'],
			$order->customer['lastname']);
	}

	/**
	 * Returns customer object
	 *
	 * @param order_ORIGIN $order
	 * @param              $paymentType
	 *
	 * @return WirecardCEE_Stdlib_ConsumerData
	 */
	public function getConsumerData($order, $paymentType)
	{
		$consumerData = new WirecardCEE_Stdlib_ConsumerData();
		$consumerData->setIpAddress($_SERVER['REMOTE_ADDR']);
		$consumerData->setUserAgent($_SERVER['HTTP_USER_AGENT']);

		if ($this->getConfigValue('send_additional_data')
			|| ($paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::INSTALLMENT
				|| $paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::INVOICE
				|| $paymentType == WirecardCEE_Stdlib_PaymentTypeAbstract::P24)
		)
		{
			$consumerData->setEmail($order->customer['email_address']);
			$consumerData->addAddressInformation($this->_getAddress($order, 'billing'));
			$consumerData->addAddressInformation($this->_getAddress($order, 'shipping'));

			$data = $this->_getCustomerData($order->customer['id']);
			if ($data !== null)
			{
				if ($data['customers_dob'] !== '0000-00-00 00:00:00' && $data['customers_dob'] !== '1000-01-01 00:00:00')
				{
					$consumerData->setBirthDate(new DateTime($data['customers_dob']));
				}

				if (strlen($data['customers_vat_id']))
				{
					$consumerData->setTaxIdentificationNumber($data['customers_vat_id']);
				}
			}
		}

		$this->log(__METHOD__ . ':' . print_r($consumerData, true));

		return $consumerData;
	}

	/**
	 * Returns address object
	 *
	 * @param order_ORIGIN $order
	 * @param string $type
	 *
	 * @return WirecardCEE_Stdlib_ConsumerData_Address
	 */
	protected function _getAddress($order, $type = 'billing')
	{
		switch ($type)
		{
		case 'shipping':
			$address = new WirecardCEE_Stdlib_ConsumerData_Address(WirecardCEE_Stdlib_ConsumerData_Address::TYPE_SHIPPING);
			$source = $order->delivery;
			break;

		default:
			$address = new WirecardCEE_Stdlib_ConsumerData_Address(WirecardCEE_Stdlib_ConsumerData_Address::TYPE_BILLING);
			$source = $order->billing;
			break;
		}

		$address->setFirstname($source['firstname']);
		$address->setLastname($source['lastname']);
		$address->setAddress1($source['street_address']);
		$address->setZipCode($source['postcode']);
		$address->setCity($source['city']);
		$address->setCountry($source['country_iso_2']);
		if ($type == 'billing')
		{
			$address->setPhone($order->customer['telephone']);
		}

		if ($source['country_iso_2'] == 'US' || $source['country_iso_2'] == 'CA')
		{
			$deliveryState = $this->_getZoneCodeByName($source['state']);
		} else
		{
			$deliveryState = $source['state'];
		}

		if (strlen($deliveryState))
		{
			$address->setState($deliveryState);
		}

		return $address;
	}

	/**
	 * 1:1 copy of admin/includes/functions/general.php
	 * there is function to reduce stock after failed payment
	 * so kick the whole order
	 *
	 * @param            $order_id
	 * @param bool|false $restock
	 * @param bool|false $canceled
	 * @param bool|false $reshipp
	 * @param bool|false $reactivateArticle
	 */
	protected function _removeOrder($order_id,
									$restock = false,
									$canceled = false,
									$reshipp = false,
									$reactivateArticle = false)
	{
		if ($restock == 'on' || $reshipp == 'on')
		{
			// BOF GM_MOD:
			$order_query = xtc_db_query("
									SELECT DISTINCT
										op.orders_products_id,
										op.products_id,
										op.products_quantity,
										opp.products_properties_combis_id,
										o.date_purchased
									FROM " . TABLE_ORDERS_PRODUCTS . " op
										LEFT JOIN " . TABLE_ORDERS . " o ON op.orders_id = o.orders_id
										LEFT JOIN orders_products_properties opp ON opp.orders_products_id = op.orders_products_id
									WHERE
										op.orders_id = '" . xtc_db_input($order_id) . "'
		");

			while ($order = xtc_db_fetch_array($order_query))
			{
				if ($restock == 'on')
				{
					/* BOF SPECIALS RESTOCK */
					$t_query = xtc_db_query("
										SELECT
											specials_date_added
										AS
											date
										FROM " . TABLE_SPECIALS . "
										WHERE
											specials_date_added < '" . $order['date_purchased'] . "'
										AND
											products_id			= '" . $order['products_id'] . "'
				");

					if ((int)xtc_db_num_rows($t_query) > 0)
					{
						xtc_db_query("
									UPDATE " . TABLE_SPECIALS . "
									SET
										specials_quantity = specials_quantity + " . $order['products_quantity'] . "
									WHERE
										products_id = '" . $order['products_id'] . "'
					");
					}
					/* EOF SPECIALS RESTOCK */

					// check if combis exists
					$t_combis_query = xtc_db_query("
								SELECT
                                    products_properties_combis_id
                                FROM
									products_properties_combis
								WHERE
									products_id = '" . $order['products_id'] . "'
				");
					$t_combis_array_length = xtc_db_num_rows($t_combis_query);

					if ($t_combis_array_length > 0)
					{
						$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
						$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
					} else
					{
						$t_use_combis_quantity = 0;
					}

					if ($t_combis_array_length == 0 || $t_use_combis_quantity == 1
						|| ($t_use_combis_quantity == 0
							&& STOCK_CHECK == 'true'
							&& ATTRIBUTE_STOCK_CHECK != 'true')
					)
					{
						xtc_db_query("
                                    UPDATE " . TABLE_PRODUCTS . "
                                    SET
                                        products_quantity = products_quantity + " . $order['products_quantity'] . "
                                    WHERE
                                        products_id = '" . $order['products_id'] . "'
                    ");
					}

					xtc_db_query("
                                UPDATE " . TABLE_PRODUCTS . "
                                SET
                                    products_ordered = products_ordered - " . $order['products_quantity'] . "
                                WHERE
                                    products_id = '" . $order['products_id'] . "'
                ");

					if ($t_combis_array_length > 0
						&& (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true'
								&& ATTRIBUTE_STOCK_CHECK == 'true')
							|| $t_use_combis_quantity == 2)
					)
					{
						xtc_db_query("
                                    UPDATE
                                        products_properties_combis
                                    SET
                                        combi_quantity = combi_quantity + " . $order['products_quantity'] . "
                                    WHERE
                                        products_properties_combis_id = '" . $order['products_properties_combis_id'] . "' AND
                                        products_id = '" . $order['products_id'] . "'
                    ");
					}

					// BOF GM_MOD
					if (ATTRIBUTE_STOCK_CHECK == 'true')
					{
						$gm_get_orders_attributes = xtc_db_query("
															SELECT
																products_options,
																products_options_values
															FROM
																orders_products_attributes
															WHERE
																orders_id = '" . xtc_db_input($order_id) . "'
															AND
																orders_products_id = '" . $order['orders_products_id'] . "'
					");

						while ($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes))
						{
							$gm_get_attributes_id = xtc_db_query("
															SELECT
																pa.products_attributes_id
															FROM
																products_options_values pov,
																products_options po,
																products_attributes pa
															WHERE
																po.products_options_name = '"
								. $gm_orders_attributes['products_options'] . "'
																AND po.products_options_id = pa.options_id
																AND pov.products_options_values_id = pa.options_values_id
																AND pov.products_options_values_name = '"
								. $gm_orders_attributes['products_options_values'] . "'
																AND pa.products_id = '" . $order['products_id'] . "'
															LIMIT 1
						");

							if (xtc_db_num_rows($gm_get_attributes_id) == 1)
							{
								$gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

								xtc_db_query("
											UPDATE
												products_attributes
											SET
												attributes_stock = attributes_stock + " . $order['products_quantity'] . "
											WHERE
												products_attributes_id = '"
									. $gm_attributes_id['products_attributes_id'] . "'
							");
							}
						}
					}
					if ($reactivateArticle == 'on')
					{
						$t_reactivate_product = false;

						// check if combis exists
						$t_combis_query = xtc_db_query("
									SELECT
										products_properties_combis_id
									FROM
										products_properties_combis
									WHERE
										products_id = '" . $order['products_id'] . "'
					");
						$t_combis_array_length = xtc_db_num_rows($t_combis_query);

						if ($t_combis_array_length > 0)
						{
							$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
							$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
						} else
						{
							$t_use_combis_quantity = 0;
						}

						// CHECK PRODUCT QUANTITY
						if ($t_combis_array_length == 0 || $t_use_combis_quantity == 1
							|| ($t_use_combis_quantity == 0
								&& STOCK_CHECK == 'true'
								&& ATTRIBUTE_STOCK_CHECK != 'true')
						)
						{
							$coo_get_product = new GMDataObject('products',
								array('products_id' => $order['products_id']));
							if ($coo_get_product->get_data_value('products_quantity') > 0
								&& $coo_get_product->get_data_value('products_status') == 0
							)
							{
								$t_reactivate_product = true;
							}
						}

						// CHECK COMBI QUANTITY
						if ($t_combis_array_length > 0
							&& (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true'
									&& ATTRIBUTE_STOCK_CHECK == 'true')
								|| $t_use_combis_quantity == 2)
						)
						{
							$coo_properties_control = MainFactory::create_object('PropertiesControl');
							$t_reactivate_product = $coo_properties_control->available_combi_exists($order['products_id']);
						}

						if ($t_reactivate_product)
						{
							$coo_set_product = new GMDataObject('products');
							$coo_set_product->set_keys(array('products_id' => $order['products_id']));
							$coo_set_product->set_data_value('products_status', 1);
							$coo_set_product->save_body_data();
						}
					}
					// EOF GM_MOD
				}

				// BOF GM_MOD products_shippingtime:
				if ($reshipp == 'on')
				{
					require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');
					set_shipping_status($order['products_id'], $order['products_properties_combis_id']);
				}
				// BOF GM_MOD products_shippingtime:
			}
		}

		if (!$canceled)
		{
			xtc_db_query("DELETE from " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($order_id) . "'");

			$t_orders_products_ids_sql = 'SELECT orders_products_id FROM ' . TABLE_ORDERS_PRODUCTS
				. ' WHERE orders_id = "' . xtc_db_input($order_id) . '"';
			$t_orders_products_ids_result = xtc_db_query($t_orders_products_ids_sql);
			while ($t_orders_products_ids_array = xtc_db_fetch_array($t_orders_products_ids_result))
			{
				xtc_db_query("DELETE FROM orders_products_quantity_units WHERE orders_products_id = '"
					. (int)$t_orders_products_ids_array['orders_products_id'] . "'");
				xtc_db_query('DELETE FROM orders_products_properties WHERE orders_products_id = "'
					. (int)$t_orders_products_ids_array['orders_products_id'] . '"');
			}

			// DELETE from gm_gprint_orders_*, and gm_gprint_uploads
			$coo_gm_gprint_order_manager = MainFactory::create_object('GMGPrintOrderManager');
			$coo_gm_gprint_order_manager->delete_order((int)$order_id);

			xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id = '" . (int)$order_id
				. "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " WHERE orders_id = '" . (int)$order_id
				. "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM banktransfer WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM sepa WHERE orders_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM orders_parcel_tracking_codes WHERE order_id = '" . (int)$order_id . "'");
			xtc_db_query("DELETE FROM orders_tax_sum_items WHERE order_id = '" . (int)$order_id . "'");
		}
	}

}

class GMWirecardCheckoutSeamlessException extends Exception
{
}

class GMWirecardCheckoutSeamlessUserException extends GMWirecardCheckoutSeamlessException
{
}

MainFactory::load_origin_class('GMWirecardCheckoutSeamless');