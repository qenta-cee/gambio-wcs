<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

class WirecardCheckoutSeamless_CheckoutSuccessExtenderComponent extends WirecardCheckoutSeamless_CheckoutSuccessExtenderComponent_parent
{
	function proceed()
	{
		parent::proceed();

		/** @var GMWirecardCheckoutSeamless_ORIGIN $wcs */
		$wcs = MainFactory::create_object('GMWirecardCheckoutSeamless');

		$data = $wcs->getTransactioData($this->v_data_array['orders_id']);

		if (is_array($data) && $data['paymentstate'] == WirecardCEE_QMore_ReturnFactory::STATE_PENDING)
		{
			$this->html_output_array[] = sprintf('
			<h2 class="underline overline">%s</h2>
			<div class="order_success_text">
				<p>%s</p>
			</div>', htmlspecialchars($wcs->getText('payment_pending_title')), htmlspecialchars($wcs->getText('payment_pending_info')));
		}
		$this->html_output_array[] = sprintf('<input type="hidden" id="order_id" value="%s" />', $this->v_data_array['orders_id']);
	}
}
