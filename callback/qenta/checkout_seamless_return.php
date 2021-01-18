<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

chdir('../../');
require_once('includes/application_top.php');
require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/QentaCheckoutSeamless.php';


/** @var GMQentaCheckoutSeamless_ORIGIN $qcs */
$qcs = MainFactory::create_object('GMQentaCheckoutSeamless');

/** @var CheckoutProcessProcess $coo_checkout_process */
$coo_checkout_process = MainFactory::create_object('CheckoutProcessProcess');
$coo_checkout_process->set_data('GET', $_GET);
$coo_checkout_process->set_data('POST', $_POST);

if(isset($_SESSION['tmp_oID']) && strlen($_SESSION['tmp_oID']))
{
	$q            = xtc_db_query(sprintf('SELECT * FROM %s WHERE orders_id = %d LIMIT 1', TABLE_PAYMENT_QCS,
	                                     (int)$_SESSION['tmp_oID']));
	$paymentState = null;
	if(xtc_db_num_rows($q))
	{
		$dbEntry      = xtc_db_fetch_array($q);
		$paymentState = $dbEntry['paymentstate'];
	}

	switch($paymentState)
	{
		case QentaCEE\QMore\ReturnFactory::STATE_SUCCESS:
			$coo_checkout_process->proceed();
			$t_redirect_url = $coo_checkout_process->get_redirect_url();
			break;
		case QentaCEE\QMore\ReturnFactory::STATE_PENDING:
			$_SESSION['cart']->reset(true);
			$t_redirect_url = xtc_href_link('checkout_qenta_checkout_seamless.php', 'return=1', 'SSL');
			break;
		default:
			$t_redirect_url = xtc_href_link('checkout_qenta_checkout_seamless.php', 'return=1', 'SSL');
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
			$qcs->updateOrdersStatus($_GET['fboOID'], $qcs->getConfigValue('status_error'), $qcs->getText('fraud_alert'));
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
