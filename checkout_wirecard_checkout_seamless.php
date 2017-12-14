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

require_once('includes/application_top.php');

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/WirecardCheckoutSeamless.php';

$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_1_CHECKOUT_SUCCESS);
$GLOBALS['breadcrumb']->add(NAVBAR_TITLE_2_CHECKOUT_SUCCESS);

// if the customer is not logged on, redirect them to the shopping cart page
if(isset($_SESSION['customer_id']) === false)
{
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

$smarty = new Smarty;
$smarty->assign('language', $_SESSION['language']);

/** @var GMWirecardCheckoutSeamless_ORIGIN $seamless */
$seamless = MainFactory::create_object('GMWirecardCheckoutSeamless');

if(isset($_GET['return']))
{
	$q            = xtc_db_query(sprintf('SELECT * FROM %s WHERE orders_id = %d LIMIT 1', TABLE_PAYMENT_WCS,
	                                     (int)$_SESSION['tmp_oID']));
	$paymentState = null;
	if(xtc_db_num_rows($q))
	{
		$dbEntry      = xtc_db_fetch_array($q);
		$paymentState = $dbEntry['paymentstate'];
		$message      = $dbEntry['message'];

		$smarty->assign('CHECKOUT_HEADER', '');
		$smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
		switch($paymentState)
		{
			case 'INIT':
				$smarty->assign('CHECKOUT_TITLE', $seamless->getText('checkout_noconfirm_title'));
				$smarty->assign('CHECKOUT_CONTENT', $seamless->getText('checkout_noconfirm_content'));
				break;

            case WirecardCEE_QMore_ReturnFactory::STATE_PENDING:
                $smarty->assign('CHECKOUT_TITLE', $seamless->getText('payment_pending_title'));
                $smarty->assign('CHECKOUT_CONTENT', $seamless->getText('payment_pending_info'));
                break;

			case WirecardCEE_QMore_ReturnFactory::STATE_CANCEL:
				$smarty->assign('CHECKOUT_TITLE', $seamless->getText('checkout_cancel_title'));
				$smarty->assign('CHECKOUT_CONTENT', $seamless->getText('checkout_cancel_content'));
				break;

			case WirecardCEE_QMore_ReturnFactory::STATE_FAILURE:
				/** @var $return WirecardCEE_QMore_Return_Failure */
				$smarty->assign('CHECKOUT_TITLE', $seamless->getText('checkout_failure_title'));
				$smarty->assign('CHECKOUT_CONTENT', $message);
				break;

			default:
				break;
		}
	}
	else
	{
		$smarty->assign('CHECKOUT_TITLE', $seamless->getText('checkout_failure_title'));
		$smarty->assign('CHECKOUT_CONTENT', $seamless->getText('checkout_failure_content'));
		$seamless->_log('no confirmation received, check firewall settings');
	}
	$smarty->assign('SHOW_STEPS', false);
}
else
{
	$iFrame = '<iframe name="' . MODULE_PAYMENT_WCP_WINDOW_NAME . '" src="' . $initResponse->getRedirectUrl()
	          . '" width="100%" height="660" border="0" frameborder="0"></iframe>';
	$smarty->assign('FORM_ACTION', $iFrame);
	$smarty->assign('CHECKOUT_TITLE', $seamless->getText('confirm_title'));
	$smarty->assign('CHECKOUT_HEADER', '');
	$smarty->assign('CHECKOUT_CONTENT', '');
	$smarty->assign('IFRAME', true);
	$smarty->assign('SHOW_STEPS', true);
}
$smarty->assign('BUTTON_CONTINUE', $seamless->getText('button_continue'));
$smarty->assign('BUTTON_CLOSE', $seamless->getText('button_close'));
$smarty->assign('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CHECKOUT'));
$smarty->assign('tpl_path', 'templates/'.CURRENT_TEMPLATE.'/');
$smarty->caching = 0;
$t_main_content  = $smarty->fetch(CURRENT_TEMPLATE . '/module/checkout_wirecard_checkout_seamless.html');

$coo_layout_control = MainFactory::create_object('LayoutContentControl');
$coo_layout_control->set_data('GET', $_GET);
$coo_layout_control->set_data('POST', $_POST);
$coo_layout_control->set_('coo_breadcrumb', $GLOBALS['breadcrumb']);
$coo_layout_control->set_('coo_product', $GLOBALS['product']);
$coo_layout_control->set_('coo_xtc_price', $GLOBALS['xtPrice']);
$coo_layout_control->set_('c_path', $GLOBALS['cPath']);
$coo_layout_control->set_('main_content', $t_main_content);
$coo_layout_control->set_('request_type', $GLOBALS['request_type']);
$coo_layout_control->proceed();

$t_redirect_url = $coo_layout_control->get_redirect_url();
if(empty($t_redirect_url) === false)
{
	xtc_redirect($t_redirect_url);
}
else
{
	echo $coo_layout_control->get_response();
}
