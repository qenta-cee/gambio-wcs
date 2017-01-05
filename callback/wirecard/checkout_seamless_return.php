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

chdir('../../');
require_once('includes/application_top.php');
require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/WirecardCheckoutSeamless.php';


/** @var GMWirecardCheckoutSeamless_ORIGIN $wcs */
$wcs = MainFactory::create_object('GMWirecardCheckoutSeamless');

/** @var CheckoutProcessProcess $coo_checkout_process */
$coo_checkout_process = MainFactory::create_object('CheckoutProcessProcess');
$coo_checkout_process->set_data('GET', $_GET);
$coo_checkout_process->set_data('POST', $_POST);

if(isset($_SESSION['tmp_oID']) && strlen($_SESSION['tmp_oID']))
{
	$q            = xtc_db_query(sprintf('SELECT * FROM %s WHERE orders_id = %d LIMIT 1', TABLE_PAYMENT_WCS,
	                                     (int)$_SESSION['tmp_oID']));
	$paymentState = null;
	if(xtc_db_num_rows($q))
	{
		$dbEntry      = xtc_db_fetch_array($q);
		$paymentState = $dbEntry['paymentstate'];
	}

	switch($paymentState)
	{
		case WirecardCEE_QMore_ReturnFactory::STATE_SUCCESS:
			$coo_checkout_process->proceed();
			$t_redirect_url = $coo_checkout_process->get_redirect_url();
			break;
		case WirecardCEE_QMore_ReturnFactory::STATE_PENDING:
			$_SESSION['cart']->reset(true);
			$t_redirect_url = xtc_href_link('checkout_wirecard_checkout_seamless.php', 'return=1', 'SSL');
			break;
		default:
			$t_redirect_url = xtc_href_link('checkout_wirecard_checkout_seamless.php', 'return=1', 'SSL');
			break;
	}
} else {
	// if tmp_oOID not present cart has been opened during payment => manipulation attempt
	// we could ignore this because our order has been created before starting the
	// payment process, manipulations are not possible/useless
	// however reset order state and leave comment for shop owner
	if (isset($_GET['fboOID']) && strlen($_GET['fboOID']))
	{
		$ordersId = (int)$_GET['fboOID'];
		if (is_numeric($ordersId))
		{
			$wcs->updateOrdersStatus($_GET['fboOID'], $wcs->getConfigValue('status_error'), $wcs->getText('fraud_alert'));
		}
	}
	$coo_checkout_process->proceed();
	$t_redirect_url = $coo_checkout_process->get_redirect_url();
}

xtc_db_close();
?>
<html>
	<head>
	</head>
	<body onLoad="window.parent.location.href = '<?php echo $t_redirect_url; ?>'">
	</body>
</html>