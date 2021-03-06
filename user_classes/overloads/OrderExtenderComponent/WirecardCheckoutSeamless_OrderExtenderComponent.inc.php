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


class WirecardCheckoutSeamless_OrderExtenderComponent extends WirecardCheckoutSeamless_OrderExtenderComponent_parent
{
	public function proceed()
	{
		global $order;

		require_once DIR_FS_CATALOG .'/includes/classes/WirecardCheckoutSeamless_Helper.php';

		if (!preg_match('/^wcs/', $order->info['payment_method'])) {
			return;
		}

		/** @var GMWirecardCheckoutSeamless_ORIGIN $wcs */
		$wcs = MainFactory::create_object('GMWirecardCheckoutSeamless');

		$data = $wcs->getTransactioData($this->v_data_array['GET']['oID']);
		if ($data === null) {
			return;
		}

		$response = json_decode($data['response'], true);
		$request = json_decode($data['request'], true);

		$orderDetails = $wcs->getOrderDetails($data['ordernumber']);

		$wcsOrder = $orderDetails->getOrder();
		if (!count($wcsOrder->getData())) {
			$response = $request;
		} else {
			$response['customerStatement'] = $wcsOrder->getCustomerStatement();
			$response['orderReference'] = $wcsOrder->getOrderReference();
		}

		$blacklist = array('responseFingerprintOrder', 'responseFingerprint', 'orders_id', 'requestFingerprintOrder',
			'requestFingerprint', 'pluginVersion', 'successUrl', 'pendingUrl', 'cancelUrl', 'serviceUrl', 'failureUrl',
			'customerId', 'shopId', 'confirmUrl');

		$payments = $orderDetails->getOrder()->getPayments()->getArray();
		usort($payments, function ($a, $b) {
			return $a->getTimeCreated() > $b->getTimeCreated();
		});

		$credits = $orderDetails->getOrder()->getCredits()->getArray();
		usort($credits, function ($a, $b) {
			return $a->getTimeCreated() > $b->getTimeCreated();
		});

		if (WirecardCheckoutSeamless_Helper::checkVersionBelow(25)) {
			$this->layoutOld($wcs, $response, $orderDetails, $blacklist, $payments, $credits);
		} else {
			$this->layout25($wcs, $response, $orderDetails, $blacklist, $payments, $credits);
		}
	}

	private function layoutOld($wcs, $response, $orderDetails, $blacklist, $payments, $credits)
	{

		parent::proceed();
		?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
                        <tr>
                                <td class="dataTableHeadingContent" style="border-right: 0px;">
                                        Wirecard Checkout Seamless
                                </td>
                        </tr>
                </table>
                <?php

		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gm_border dataTableRow">';

		foreach($response as $k => $v)
		{
			if(in_array($k, $blacklist))
			{
				continue;
			}
			?>
			<tr>
				<td width="80" class="main gm_strong" valign="top">
					<?php echo htmlspecialchars($k); ?>
				</td>
				<td colspan="5" class="main" valign="top">
					<?php echo htmlspecialchars($v); ?>
				</td>
			</tr>
			<?php
		}

		if($orderDetails->hasFailed())
		{
			print "</table>";
			return;
		}

		echo '<tr><td colspan="2">';
		foreach($orderDetails->getOrder()->getOperationsAllowed() as $op)
		{
			if ($op != 'REFUND')
			{continue;}

			printf('<input type="text" style="width: 80px;" value=""/>');
			printf('<a class="button wcs-backend-op" data-payment="%s" data-operation="%s" href="#" style="display: inline-block;">%s</a></div>',
				htmlspecialchars(json_encode($orderDetails->getOrder()->getData())), htmlspecialchars(strtolower($op)), $wcs->getText(strtolower($op)));
		}
		echo '</td></tr>';
		echo '</table>';

		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="gm_border dataTableRow">';
		echo '<tr><td class="main" colspan="6">';
		?>
		<?php if(!empty($_SESSION['wcs_messages'])): ?>
		<style>
			div.messages {
				border:        2px solid red;
				background:    #ffa;
				padding:       1ex 1em;
				margin-bottom: 1em;
			}

			div.messages p.message {
				margin: 0;
			}
		</style>
		<div class="messages">
			<?php
			foreach($_SESSION['wcs_messages'] as $message)
			{
				echo '<p class="message">' . $message . '</p>';
			}
			?>
		</div>
		<?php $_SESSION['wcs_messages'] = array(); ?>
	<?php endif ?>
                </td>
                <?php
		echo '</tr>';
		printf('<tr><td class="main">%s</td></tr>', $wcs->getText('payments'));
		echo '<tr>';
		printf('<th class="dataTableHeadingContent" style="text-align: right;">%s</th>', $wcs->getText('paymentnumber'));
		printf('<th class="dataTableHeadingContent" style="text-align: right;">%s</th>', $wcs->getText('approveamount'));
		printf('<th class="dataTableHeadingContent" style="text-align: right;">%s</th>', $wcs->getText('depositamount'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('orderstate'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('timecreated'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('operation'));
		echo '</tr>';

		foreach($payments as $idx => $p)
		{
			/** @var WirecardCEE_QMore_Response_Backend_Order_Payment $p */

			echo '<tr>';
			printf('<td class="main" style="text-align: right;">%s</td>', $p->getPaymentNumber());
			printf('<td class="main" style="text-align: right;">%s</td>', $p->getApproveAmount());
			printf('<td class="main" style="text-align: right;">%s</td>', $p->getDepositAmount());
			printf('<td class="main" style="text-align: center;">%s</td>', $wcs->getText($p->getState()));
			printf('<td class="main">%s</td>', strftime('%d.%m.%Y %R', $p->getTimeCreated()->getTimestamp()));
			printf('<td class="main" valign="top">');

			//foreach(array('approveReversal', 'deposit', 'depositReversal', 'refund', 'refundReversal') as $op)
			foreach($p->getOperationsAllowed() as $op)
			{
				if (!strlen($op))
				{continue;}

				if($op == 'DEPOSIT')
				{
					printf('<input type="text" style="width: 80px;" value="%s"/>', $p->getApproveAmount());
				}
				printf('<a class="button wcs-backend-op" data-payment="%s" data-operation="%s" href="#" style="display: inline-block;">%s</a></div>',
					htmlspecialchars(json_encode($p->getData())), htmlspecialchars(strtolower($op)), $wcs->getText(strtolower($op)));
			}

			printf('</td></tr>');
		}

		printf('<tr><td class="main">%s</td></tr>', $wcs->getText('credits'));
		echo '<tr>';
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('creditnumber'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('amount'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('creditstate'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('timecreated'));
		printf('<th class="dataTableHeadingContent">%s</th>', $wcs->getText('operation'));
		echo '</tr>';

		foreach($credits as $idx => $c)
		{
			/** @var WirecardCEE_QMore_Response_Backend_Order_Credit $c */

			echo '<tr>';
			printf('<td class="main" style="text-align: right;">%s</td>', $c->getCreditNumber());
			printf('<td class="main" style="text-align: right;">%s</td>', $c->getAmount());
			printf('<td class="main" style="text-align: center;">%s</td>', $wcs->getText($c->getState()));
			printf('<td class="main">%s</td>', strftime('%d.%m.%Y %R', $c->getTimeCreated()->getTimestamp()));
			printf('<td class="main" valign="top">');

			foreach($c->getOperationsAllowed() as $op)
			{
				if (!strlen($op))
				{continue;}

				printf('<a class="button wcs-backend-op" data-payment="%s" data-operation="%s" href="#" style="display: inline-block;">%s</a></div>',
					htmlspecialchars(json_encode($c->getData())), htmlspecialchars(strtolower($op)), $wcs->getText(strtolower($op)));
			}
			printf('</td></tr>');
		}
		echo '</table>';

		?>
		<script type="text/javascript">
				$(function () {
						$('.wcs-backend-op').on('click', function (evt) {
								var target = evt.target;
								var data = {
										operation: $(target).attr('data-operation'),
										payment:   $(target).attr('data-payment')
								};

								if ($(target).attr('data-operation') == 'deposit' ||
									$(target).attr('data-operation') == 'refund') {
										data.amount = $(target).prev().val();
								}

								$.ajax({
										type:     "POST",
										url:      'wirecard_checkout_seamless_backend.php',
										dataType: 'json',
										context:  null,
										data:     data
								}).always(function (jqXHR, textStatus, errorThrown) {
										location.reload(true);
								});

								return false;
						});
				});
		</script>
		<?php
	}

	private function layout25($wcs, $response, $orderDetails, $blacklist, $payments, $credits)
	{
		if (!empty($_SESSION['wcs_messages'])) {
			?>
			<div class="message_stack_container messages">
				<?php
				foreach ($_SESSION['wcs_messages'] as $message) {
					echo '<div class="alert alert-danger">' . $message . '</div>';
				}
				?>
			</div>
			<?php
			$_SESSION['wcs_messages'] = array();
		}

		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTableRow">';

		foreach ($response as $k => $v) {
			if (in_array($k, $blacklist)) {
				continue;
			}
			?>
			<tr>
				<td width="80" class="main gm_strong" valign="top">
					<?php echo htmlspecialchars($k); ?>
				</td>
				<td colspan="5" class="main" valign="top">
					<?php echo htmlspecialchars($v); ?>
				</td>
			</tr>
			<?php
		}

		if ($orderDetails->hasFailed()) {
			print "</table>";
			return;
		}

		echo '</table>';

		echo '</div></div><div class="frame-wrapper"><div class="frame-head"><label class="title">Wirecard Checkout Seamless - ' . $wcs->getText('payments') . '</label></div><div class="frame-content gx-container table-content">';

		echo '<table>';
		echo '<thead>';
		echo '<tr>';
		printf('<th style="text-align: right;">%s</th>', $wcs->getText('paymentnumber'));
		printf('<th style="text-align: right;">%s</th>', $wcs->getText('approveamount'));
		printf('<th style="text-align: right;">%s</th>', $wcs->getText('depositamount'));
		printf('<th>%s</th>', $wcs->getText('orderstate'));
		printf('<th>%s</th>', $wcs->getText('timecreated'));
		printf('<th>%s</th>', $wcs->getText('operation'));
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		foreach($payments as $idx => $p)
		{
			/** @var WirecardCEE_QMore_Response_Backend_Order_Payment $p */

			echo '<tr>';
			printf('<td style="text-align: right;">%s</td>', $p->getPaymentNumber());
			printf('<td style="text-align: right;">%s</td>', $p->getApproveAmount());
			printf('<td style="text-align: right;">%s</td>', $p->getDepositAmount());
			printf('<td style="text-align: center;">%s</td>', $wcs->getText($p->getState()));
			printf('<td>%s</td>', strftime('%d.%m.%Y %R', $p->getTimeCreated()->getTimestamp()));
			printf('<td valign="top">');

			foreach($p->getOperationsAllowed() as $op)
			{
				if (!strlen($op))
					{continue;}

				if($op == 'DEPOSIT')
				{
					printf('<input type="text" style="width: 100px;" value="%s"/>', $p->getApproveAmount());
				}

				printf('<span style="height: auto; margin-bottom: 5px;" class="btn cursor-pointer wcs-backend-op" data-payment="%s" data-operation="%s" >%s</span>',
					htmlspecialchars(json_encode($p->getData())), htmlspecialchars(strtolower($op)), $wcs->getText(strtolower($op)));
			}

			printf('</td></tr>');
		}

		echo '</tbody></table></div></div>';
		echo '<div class="frame-wrapper"><div class="frame-head"><label class="title pull-left">Wirecard Checkout Seamless - ' . $wcs->getText('credits') . '</label>';

		foreach ($orderDetails->getOrder()->getOperationsAllowed() as $op) {
			if ($op != 'REFUND') {
				continue;
			}

			echo '<label class="pull-right head-link" style="border-color: #468847 !important;">';
			printf('<a href="#" target="_blank" style="color: #468847 !important;" data-gx-compatibility="orders/orders_wcs">%s</a>', $wcs->getText('credit_add'));
			echo '</label>';
		}


		echo '</div><div class="frame-content gx-container table-content">';
		echo '<table>';
		echo '<thead>';
		echo '<tr>';
		printf('<th >%s</th>', $wcs->getText('creditnumber'));
		printf('<th>%s</th>', $wcs->getText('amount'));
		printf('<th>%s</th>', $wcs->getText('creditstate'));
		printf('<th>%s</th>', $wcs->getText('timecreated'));
		printf('<th>%s</th>', $wcs->getText('operation'));
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		foreach($credits as $idx => $c)
		{
			/** @var WirecardCEE_QMore_Response_Backend_Order_Credit $c */

			echo '<tr>';
			printf('<td style="text-align: right;">%s</td>', $c->getCreditNumber());
			printf('<td style="text-align: right;">%s</td>', $c->getAmount());
			printf('<td style="text-align: center;">%s</td>', $wcs->getText($c->getState()));
			printf('<td>%s</td>', strftime('%d.%m.%Y %R', $c->getTimeCreated()->getTimestamp()));
			printf('<td valign="top">');

			foreach($c->getOperationsAllowed() as $op)
			{
				if (!strlen($op))
					{continue;}

				printf('<span style="height: auto; margin-top: 8px;" class="btn cursor-pointer wcs-backend-op" data-payment="%s" data-operation="%s" >%s</span>',
					htmlspecialchars(json_encode($c->getData())), htmlspecialchars(strtolower($op)), $wcs->getText(strtolower($op)));
			}
			printf('</td></tr>');
		}
		echo '</tbody>';
		echo '</table>';
		?>

		<form class="grid hidden" data-gx-widget="checkbox" action="#" name="add_credit_form" id="add_credit_form">
			<fieldset class="span12">
				<div class="control-group">
					<input type="hidden" id="refund_payment" name="payment" value="<?= htmlspecialchars(json_encode($orderDetails->getOrder()->getData())) ?>" />
					<label><?= $wcs->getText('amount') ?></label>
					<input type="text" id="refund_amount" name="amount" value="" />
				</div>

			</fieldset>
		</form>

		<script type="text/javascript">
			$(function () {
				$('.wcs-backend-op').on('click', function (evt) {
					var target = evt.target;
					var data = {
						operation: $(target).attr('data-operation'),
						payment:   $(target).attr('data-payment')
					};

					if ($(target).attr('data-operation') == 'deposit') {
						data.amount = $(target).prev().val();
					}

					$.ajax({
						type:     "POST",
						url:      'wirecard_checkout_seamless_backend.php',
						dataType: 'json',
						context:  null,
						data:     data
					}).always(function (jqXHR, textStatus, errorThrown) {
						location.reload(true);
					});

					return false;
				});
			});
		</script>
		<?php

		$this->v_output_buffer['below_order_info_heading'] = 'Wirecard Checkout Seamless';
		$this->addContent();
		parent::proceed();
	}

}