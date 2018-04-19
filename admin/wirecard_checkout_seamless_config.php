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

define('PAGE_URL', HTTP_SERVER . DIR_WS_ADMIN . basename(__FILE__));

define('WCS_SUPPORT_URL', HTTP_SERVER . DIR_WS_ADMIN . 'wirecard_checkout_seamless_support.php');
define('WCS_TRANSFERFUND_URL', HTTP_SERVER . DIR_WS_ADMIN . 'wirecard_checkout_seamless_transferfund.php');

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

if(!isset($_SESSION['wcs_config_values_tmp']))
{
	$_SESSION['wcs_config_values_tmp'] = array();
}

/** @var GMWirecardCheckoutSeamless_ORIGIN $wcs */
$wcs    = MainFactory::create_object('GMWirecardCheckoutSeamless');
$config = $wcs->getConfig();

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	isset($_SESSION['coo_page_token']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token']);

	if(isset($_POST['testconfig']))
	{
		try
		{
			$wcs->testConfig();
			$_SESSION[$messages_ns] = array($wcs->getText('configtest_ok'));
		}
		catch(Exception $e)
		{
			$_SESSION[$messages_ns] = array($e->getMessage());
		}
	}
	else
	{
		if(isset($_POST['wcs']) && is_array($_POST['wcs']))
		{
			$errors = array();
			foreach($_POST['wcs'] as $k => $v)
			{
				try
				{
					$_SESSION['wcs_config_values_tmp'][$k] = $v;
					$wcs->validateConfigValue($k, $v, $_POST['wcs']['configtype']);
				}
				catch(GMWirecardCheckoutSeamlessException $e)
				{
					$errors[$k] = $e->getMessage();
				}
			}

			if(count($errors))
			{
				$_SESSION[$messages_ns] = $errors;
			}
			else
			{
				foreach($_POST['wcs'] as $k => $v)
				{
					$wcs->setConfigValue($k, $v);
				}
				$_SESSION['wcs_config_values_tmp'] = array();
			}
		}
	}
	xtc_redirect(PAGE_URL);
}

$messages               = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$tmp_values                        = $_SESSION['wcs_config_values_tmp'];
$_SESSION['wcs_config_values_tmp'] = array();
$orders_stati                      = $wcs->getOrdersStati();

ob_start();
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel='stylesheet' type='text/css' href='html/assets/styles/legacy/stylesheet.css'>
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
							##configtype
						</td>
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							<a href="wirecard_checkout_seamless_transferfund.php">##title_transferfund</a>
						</td>
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							<a href="wirecard_checkout_seamless_support.php">##title_support</a>
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
								<table class="gx-configuration" data-gx-extension="visibility_switcher">
									<?php
									foreach($config as $group => $fields)
									{
										printf('<th class="dataTableContent_gm" colspan="2">##config_group_%s</th>', $group);
										foreach($fields as $field => $opts)
										{
											$style = '';
											$value = isset($tmp_values[$field]) ? $tmp_values[$field] : $opts['value'];
											if(isset($opts['width']))
											{
												$style = 'width: ' . $opts['width'];
											}
											$cssClass = isset($messages[$field]) ? 'error' : '';

											printf('<tr class="visibility_switcher"><td class="dataTableContent_gm configuration-label">##%s', $field);

											if($opts['required']) {
												print '<span class="fieldRequired">##field_required</span>';
											}

											print '</td><td class="dataTableContent_gm">';




											switch($opts['type'])
											{
												case 'order_status':
													printf('<select name="wcs[%s]" style="%s" class="%s">', $field,
														$style, $cssClass);
													foreach($orders_stati as $status_id => $name)
													{
														printf('<option value="##%s" %s>%s</option>', $status_id,
															$value == $status_id ? 'selected="selected"' : '',
															htmlspecialchars($name));
													}
													print ('</select>');
													break;

												case 'select':
													printf('<select name="wcs[%s]" style="%s" class="%s">', $field,
														$style, $cssClass);
													foreach($opts['options'] as $v => $lbl)
													{
														printf('<option value="%s" %s>##%s</option>',
															htmlspecialchars($v),
															$value == $v ? 'selected="selected"' : '',
															htmlspecialchars($lbl));
													}
													print ('</select>');
													break;

												case 'checkbox':
													printf('<input name="wcs[%s]" type="hidden" value="0"/>',
														$field);

													printf('<div class="gx-container checkbox-switch-wrapper"><div data-gx-widget="checkbox"><input id="%s" name="wcs[%s]" type="%s" value="1" style="%s" class="%s" %s/></div></div>',
														$field, $field, $opts['type'], $style, $cssClass,
														$value ? 'checked="checked"' : '');
													break;

												default:
													printf('<input id="%s" name="wcs[%s]" type="%s" value="%s" style="%s" class="pull-left %s"/>',
														$field, $field, $opts['type'], htmlspecialchars($value),
														$style, $cssClass);
											}

											if(isset($opts['docref']))
											{
												printf('<a href="%s" target="_blank">', $opts['docref']);
											}

											if(strlen($wcs->getText($field . '_desc')))
											{
												printf('<span class="tooltip-icon" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">%s</span>', $wcs->getText($field . '_desc'));
											}

											if(isset($opts['docref']))
											{
												print '</a>';
											}



											print "</td></tr>\n";
										}
									}

									print "\n";

									?>
								</table>
								<div class="grid" style="margin-top: 24px">
									<?php print $wcs->getVersion(); ?>
									<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
									<input type="submit" class="button btn btn-primary pull-right" name="saveconfig" value="##saveconfig" />
									<input type="submit" class="button btn btn-primary pull-right" name="testconfig" value="##testconfig" />
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

