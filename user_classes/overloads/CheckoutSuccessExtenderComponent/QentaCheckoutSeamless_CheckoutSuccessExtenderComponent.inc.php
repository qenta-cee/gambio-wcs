<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

class QentaCheckoutSeamless_CheckoutSuccessExtenderComponent extends QentaCheckoutSeamless_CheckoutSuccessExtenderComponent_parent
{
	function proceed()
	{
		parent::proceed();

		/** @var GMQentaCheckoutSeamless_ORIGIN $qcs */
		$qcs = MainFactory::create_object('GMQentaCheckoutSeamless');

		$data = $qcs->getTransactioData($this->v_data_array['orders_id']);

		if (is_array($data) && $data['paymentstate'] == QentaCEE\QMore\ReturnFactory::STATE_PENDING)
		{
			$this->html_output_array[] = sprintf('
			<h2 class="underline overline">%s</h2>
			<div class="order_success_text">
				<p>%s</p>
			</div>', htmlspecialchars($qcs->getText('payment_pending_title')), htmlspecialchars($qcs->getText('payment_pending_info')));
		}
		$this->html_output_array[] = sprintf('<input type="hidden" id="order_id" value="%s" />', $this->v_data_array['orders_id']);
	}
}
