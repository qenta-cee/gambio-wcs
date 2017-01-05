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

require_once 'includes/application_top.php';
require_once(DIR_FS_ADMIN . 'includes/gm/classes/GMModulesManager.php');
require_once(DIR_FS_ADMIN . 'includes/gm/gm_modules/gm_modules_structure.php');
require_once DIR_FS_CATALOG .'/includes/classes/WirecardCheckoutSeamless_Helper.php';

if (WirecardCheckoutSeamless_Helper::checkVersionBelow(25)) {
	require_once(DIR_FS_CATALOG . 'includes/classes/class.phpmailer.php');
}

require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');

define('PAGE_URL', HTTP_SERVER . DIR_WS_ADMIN . basename(__FILE__));

if(isset($_SESSION['coo_page_token']))
{
	$t_page_token = $_SESSION['coo_page_token']->generate_token();
}
else
{
	$t_page_token = '';
}

$messages_ns = 'messages_' . basename(__FILE__);
if(!isset($_SESSION[$messages_ns]))
{
	$_SESSION[$messages_ns] = array();
}

/** @var GMWirecardCheckoutSeamless_ORIGIN $wcs */
$wcs    = MainFactory::create_object('GMWirecardCheckoutSeamless');
$config = $wcs->getConfig();

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	isset($_SESSION['coo_page_token']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token']);

	if(isset($_POST['sendrequest']) && isset($_POST['to']) && isset($_POST['description']))
	{
		if(!filter_var($_POST['to'], FILTER_VALIDATE_EMAIL))
		{
			$_SESSION[$messages_ns] = array(sprintf($wcs->getText('email_invalid'), $wcs->getText('support_to')));
			xtc_redirect(PAGE_URL);
		}
		if(!filter_var($_POST['from'], FILTER_VALIDATE_EMAIL))
		{
			$_SESSION[$messages_ns] = array(sprintf($wcs->getText('email_invalid'), $wcs->getText('support_from')));
			xtc_redirect(PAGE_URL);
		}

		if(strlen($_POST['reply_to']) && !filter_var($_POST['reply_to'], FILTER_VALIDATE_EMAIL))
		{
			$_SESSION[$messages_ns] = array(
				sprintf($wcs->getText('email_invalid'), $wcs->getText('support_reply_to'))
			);
			xtc_redirect(PAGE_URL);
		}

		$body     = (string)$_POST['description'];
		$reply_to = $_POST['reply_to'];
		$to       = $_POST['to'];
		$from     = $_POST['from'];

		$body .= sprintf("\n\n%s\n\n", $wcs->getText('support_pluginconfig'));
		$body .= $wcs->getVersion();
		$body .= "\n\n";
		$body .= $wcs->getConfigString();
		$body .= sprintf("\n\n%s\n\n", $wcs->getText('support_installed_wcs'));
		/** @var GMModuleManager_ORIGIN $coo_module_manager */
		$coo_module_manager = new GMModuleManager('payment');

		$modules      = $coo_module_manager->get_modules_installed();
		$myModules    = array_filter($modules, function ($m)
		{
			return preg_match('/^wcs_/', $m);
		});
		$otherModules = array_filter($modules, function ($m)
		{
			return !preg_match('/^wcs_/', $m);
		});
		sort($myModules);
		sort($otherModules);
		foreach($myModules as $m)
		{

			$mInfo = new objectInfo($coo_module_manager->get_module_data_by_name($m));
			$body .= sprintf("%s\n", strip_tags($mInfo->title));
			$body = str_replace('&nbsp;', '', $body);
			foreach($mInfo->keys as $i)
			{
				$body .= sprintf("\t%s: %s\n", $i['title'], $i['value']);
			}

			$body .= "\n";
		}

		$body .= sprintf("\n%s\n", $wcs->getText('support_installed_modules'));

		foreach($otherModules as $m)
		{
			$body .= "$m\n";
		}

		$bodyHtml = str_replace(array("\n", "\t"), array("<br/>\n", '&nbsp;&nbsp;&nbsp;&nbsp;'),
		                        htmlspecialchars($body));

		// use default from name, if STORE_OWNER is not configured
		$from_name = strlen(STORE_OWNER) ? STORE_OWNER : 'gambio wcs';
		xtc_php_mail($from, $from_name, $to, 'wirecard', null, $reply_to, '', null, null, $wcs->getText('support_subject'),
		             $bodyHtml, $body);

		$_SESSION[$messages_ns] = array($wcs->getText('support_send_ok'));
	}

	xtc_redirect(PAGE_URL);
}

$messages               = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

ob_start();
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<script type="text/javascript" src="includes/general.js"></script>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
	<!-- header //-->
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
	<!-- header_eof //-->

	<!-- body //-->
	<table border="0" width="100%" cellspacing="2" cellpadding="2" class="miscellaneous">
		<tr>
			<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
				<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</table>
			</td>
			<!-- body_text //-->
			<td class="boxCenter" width="100%" valign="top">

				<div class="pageHeading" style="background-image:url(images/gm_icons/gambio.png)">##title</div>
				<br />

        <span class="main">
                <table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr class="dataTableHeadingRow">
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							<a href="wirecard_checkout_seamless_config.php">##configtype</a>
						</td>
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							<a href="wirecard_checkout_seamless_transferfund.php">##title_transferfund</a>
						</td>
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							##title_support
						</td>
					</tr>
				</table>

				<div class="message_stack_container breakpoint-small" id="wcs-message-container">
					<?php foreach($messages as $msg): ?>
						<div class="alert alert-success breakpoint-small"><?php echo $msg ?></div>
					<?php endforeach; ?>
				</div>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="breakpoint-small multi-table-wrapper">
					<tr class="gx-container">
						<td style="font-size: 12px; text-align: justify">

							<form id="wcs_config" action="<?php echo PAGE_URL ?>" method="post" class="shop-key-form">
								<table class="gx-configuration">
									<tr style="display: none">
										<td class="dataTableContent_gm configuration-label">
											&nbsp;
										</td>
										<td class="dataTableContent_gm">
											&nbsp;
										</td>
									</tr>
									<tr>
										<td class="dataTableContent_gm configuration-label">
											##support_to</a>
										</td>
										<td class="dataTableContent_gm">
											<select id="wcs-to"	name="to" style="width: 290px;">
												<option value="support.at@wirecard.com">
													support.at@wirecard.com
												</option>
												<option value="support.de@wirecard.com">
													support.de@wirecard.com
												</option>
											</select>
										</td>
									</tr>
									<tr>
										<td class="dataTableContent_gm configuration-label">
											##support_reply_to
										</td>
										<td class="dataTableContent_gm">
											<input class="pull-left" type="text" id="wcs-reply" style="width: 290px;" name="reply_to" />
										</td>
									</tr>
									<tr class="visibility_switcher">
										<td class="dataTableContent_gm configuration-label">
											##support_from
										</td>
										<td class="dataTableContent_gm">
											<input class="pull-left" type="text" id="wcs-from" name="from" style="width: 290px;" value="<?php echo EMAIL_FROM ?>" />
										</td>
									</tr>
									<tr class="visibility_switcher">
										<td class="dataTableContent_gm configuration-label">
											##support_description
										</td>
										<td class="dataTableContent_gm">
											<textarea id="wcs-description" name="description" style="width: 290px; height: 290px;"></textarea>
										</td>
									</tr>
								</table>
								<div class="grid" style="margin-top: 24px">
									<?php print $wcs->getVersion(); ?>
									<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
									<input type="submit" id="wcs-transfer-send" class="button btn btn-primary pull-right" name="sendrequest" value="##support_request_send" />
								</div>
							</form>
						</td>
					</tr>
				</table>

        </span>

			</td>
			<!-- body_text_eof //-->
		</tr>
	</table>
	<!-- body_eof //-->

	<!-- footer //-->
	<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	<!-- footer_eof //-->
	<br />
	</body>
	</html>

<?php

require(DIR_WS_INCLUDES . 'application_bottom.php');

$content = ob_get_clean();
$content = $wcs->replaceLanguagePlaceholders($content);
echo $content;

