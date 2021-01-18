<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
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
							<a href="wirecard_checkout_seamless_config.php">##configtype</a>
						</td>
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							##title_transferfund
						</td>
						<td class="dataTableHeadingContentText" style="width:1%; padding-right:20px; white-space: nowrap">
							<a href="wirecard_checkout_seamless_support.php">##title_support</a>
						</td>
					</tr>
				</table>

				<div class="message_stack_container breakpoint-small" id="wcs-message-container"></div>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="breakpoint-small multi-table-wrapper">
					<tr class="gx-container">
						<td style="font-size: 12px; text-align: justify">

								<form id="wcs_config" action="<?php echo PAGE_URL ?>" method="post">
									<table class="gx-configuration" data-gx-extension="visibility_switcher">
										<tr style="display: none">
											<td class="dataTableContent_gm configuration-label">
												&nbsp;
											</td>
											<td class="dataTableContent_gm">
												&nbsp;
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##transfer_type</a>
											</td>
											<td class="dataTableContent_gm">
												<select id="wcs-transfertype" name="transfertype">
													<?php
													foreach($wcs->getTransferTypes() as $v => $lbl)
													{
														printf('<option value="%s">%s</option>',
															htmlspecialchars($v), htmlspecialchars($lbl));
													}
													?>
												</select>
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/backend:transferfund#existingorder" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##currency
											</td>
											<td class="dataTableContent_gm">
												<select id="wcs-currency" name="currency">
													<?php
													foreach($wcs->getCurrencies() as $v => $lbl)
													{
														printf('<option value="%s">%s</option>',
															htmlspecialchars($v), htmlspecialchars($lbl));
													}
													?>
												</select>
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#currency" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##amount<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-amount" name="amount" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#amount" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##orderdescription<span class="fieldRequired" id="wcs-orderdescription-required">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-orderdescription" name="orderdescription" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#orderdescription" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##customerstatement<span class="fieldRequired" id="wcs-customerstatement-required" style="display: none;">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-customerstatement" name="customerstatement" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#customerstatement" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##creditnumber
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-creditnumber" name="creditnumber" />
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##ordernumber
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-ordernumber" name="ordernumber" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#ordernumber" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##orderreference
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-orderreference" name="orderreference" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#orderreference" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
									</table>
									<table class="gx-configuration transferfund-fields" id="fields-existingorder" data-gx-extension="visibility_switcher">
										<th class="dataTableContent_gm" colspan="2">
											##transfer_existingorder
										</th>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##sourceordernumber<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-sourceordernumber" name="sourceordernumber" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#ordernumber" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
									</table>
									<table class="gx-configuration transferfund-fields" id="fields-skrillwallet" style="display: none;" data-gx-extension="visibility_switcher">
										<th class="dataTableContent_gm" colspan="2">
											##transfer_skrillwallet
										</th>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##consumeremail<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-consumeremail" name="consumeremail" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/request_parameters#consumer_billing_data" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
									</table>
									<table class="gx-configuration transferfund-fields" id="fields-moneta" style="display: none;" data-gx-extension="visibility_switcher">
										<th class="dataTableContent_gm" colspan="2">
											##transfer_moneta
										</th>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##consumerwalletid<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-consumerwalletid" name="consumerwalletid" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/backend:transferfund#moneta" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
									</table>
									<table class="gx-configuration transferfund-fields" id="fields-sepa-ct" style="display: none;" data-gx-extension="visibility_switcher">
										<th class="dataTableContent_gm" colspan="2">
											##transfer_sepact
										</th>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##bankaccountowner<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-bankaccountowner" name="bankaccountowner" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/backend:transferfund#sepa-ct" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##bankbic<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-bankbic" name="bankbic" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/backend:transferfund#sepa-ct" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
										<tr class="visibility_switcher">
											<td class="dataTableContent_gm configuration-label">
												##bankaccountiban<span class="fieldRequired">##field_required</span>
											</td>
											<td class="dataTableContent_gm">
												<input class="pull-left" type="text" id="wcs-bankaccountiban" name="bankaccountiban" />
												<span class="tooltip-icon" style="visibility: hidden;">
													<a href="https://integration.wirecard.at/doku.php/backend:transferfund#sepa-ct" target="_blank" class="gx-container tooltip_icon info"><i class="fa fa-info-circle" style="font-size: 24px;"></i></a>
												</span>
											</td>
										</tr>
									</table>
									<div class="grid" style="margin-top: 24px">
										<?php print $wcs->getVersion(); ?>
										<?php echo xtc_draw_hidden_field('page_token', $t_page_token); ?>
										<input type="submit" id="wcs-transfer-send" class="button btn btn-primary pull-right" name="sendrequest" value="##transfer_execute" />
									</div>
								</form>
							<script type="text/javascript">
								$(function () {
									$('#wcs-transfertype').on('change', function (evt) {
										var fieldsetId = 'fields-' + this.value.toLowerCase();
										$('.transferfund-fields').each(function (index, fieldset) {
											if ($(fieldset).attr('id') == fieldsetId) {
												$(fieldset).css('display', 'table');
											} else {
												$(fieldset).css('display', 'none');
											}

											if (fieldsetId == 'fields-existingorder' || fieldsetId == 'fields-sepa-ct') {
												$('#wcs-customerstatement-required').css('display', 'none');
											} else {
												$('#wcs-customerstatement-required').css('display', 'inline');
											}
										});


									});

									$('#wcs-transfer-send').on('click', function (evt) {

										var payment = {
											amount:            $('#wcs-amount').val(),
											currency:          $('#wcs-currency').val(),
											orderDescription:  $('#wcs-orderdescription').val(),
											customerStatement: $('#wcs-customerstatement').val(),
											sourceOrderNumber: $('#wcs-sourceordernumber').val(),
											consumerEmail:     $('#wcs-consumeremail').val(),
											consumerWalletId:  $('#wcs-consumerwalletid').val(),
											orderNumber:       $('#wcs-ordernumber').val(),
											creditNumber:      $('#wcs-creditnumber').val(),
											orderReference:    $('#wcs-orderreference').val(),
											bankAccountOwner:  $('#wcs-bankaccountowner').val(),
											bankBic:           $('#wcs-bankbic').val(),
											bankAccountIban:   $('#wcs-bankaccountiban').val()
										};
										var data = {
											operation:    'transferfund',
											transfertype: $('#wcs-transfertype').val(),
											payment:      JSON.stringify(payment)
										};

										$.ajax({
											type:     "POST",
											url:      'wirecard_checkout_seamless_backend.php',
											dataType: 'json',
											context:  null,
											data:     data
										}).then(function (data, textStatus, jqXHR) {

											var div = $(document.createElement('div')).addClass('alert alert-success breakpoint-small').text(<?php echo json_encode($wcs->getText('transferfund_ok')) ?>);
											$('#wcs-message-container').empty().append(div);

										}, function (jqXHR, textStatus, errorThrown) {
											$('#wcs-message-container').empty();
											$.each(jqXHR.responseJSON, function (idx, txt) {
												var div = $(document.createElement('div')).addClass('alert alert-danger breakpoint-small').text(txt);

												$('#wcs-message-container').append(div);
											});
										});

										return false;
									});
								});
							</script>
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

