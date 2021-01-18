<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/QentaCheckoutSeamless.php';

/**
 * @see QentaCheckoutSeamless_ORIGIN
 */
class qcs_ccard_ORIGIN extends QentaCheckoutSeamless_ORIGIN
{
	protected $_defaultSortOrder = 1;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::CCARD;
	protected $_logoFilename     = 'ccard.png';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		if($this->_seamless->getConfigValue('pci3_dss_saq_a_enable'))
		{
			$content['fields'][] = array(
				'title' => '',
				'field' => sprintf('<div id="qcsIframeContainer%s"></div>', $this->code)
			);

			return $content;
		}

		$cssClass = $this->code;

		if($this->_seamless->getConfigValue('creditcard_showcardholder'))
		{
			$content['fields'][] = array(
				'title' => $this->_seamless->getText('creditcard_cardholder'),
				'field' => sprintf('<input type="text" class="%s input-text" name="qcs_cardholder" data-wcs-fieldname="cardholdername" autocomplete="off" value=""/>',
				                   $cssClass)
			);
		}
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('creditcard_pan'),
			'field' => sprintf('<input type="text" class="%s input-text" name="qcs_cardnumber" data-wcs-fieldname="pan" autocomplete="off" value=""/>',
			                   $cssClass)
		);

		$field = sprintf('<select name="qcs_expirationmonth" class="%s qcs_expirationmonth" data-wcs-fieldname="expirationMonth">',
		                 $cssClass);
		for($m = 1; $m <= 12; $m++)
		{
			$field .= sprintf('<option value="%d">%02d</option>', $m, $m);
		}
		$field .= '</select>&nbsp;';

		$field .= sprintf('<select name="qcs_expirationyear" class="%s qcs_expirationyear" data-wcs-fieldname="expirationYear">',
		                  $cssClass);
		foreach($this->getCreditCardYears() as $y)
		{
			$field .= sprintf('<option value="%d">%s</option>', $y, $y);
		}
		$field .= '</select>';

		if($this->_seamless->getConfigValue('creditcard_showcvc'))
		{
			$field .= sprintf('<div class="label qcs_label_cvc">CVC</div><input type="text" class="%s qcs_cvc input-text" name="qcs_cvc" data-wcs-fieldname="cardverifycode" autocomplete="off" value="" maxlength="4"/>',
			                  $cssClass);
		}
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('creditcard_expiry'),
			'field' => $field
		);

		if($this->_seamless->getConfigValue('creditcard_showissuedate'))
		{
			$field = sprintf('<select name="qcs_issuemonth" class="%s qcs_issuemonth" data-wcs-fieldname="issueMonth">',
			                 $cssClass);
			for($m = 1; $m <= 12; $m++)
			{
				$field .= sprintf('<option value="%d">%02d</option>', $m, $m);
			}
			$field .= '</select>&nbsp;';

			$field .= sprintf('<select name="qcs_issueyear" class="%s qcs_issueyear" data-wcs-fieldname="issueYear">',
			                  $cssClass);
			foreach($this->getCreditCardIssueYears() as $y)
			{
				$field .= sprintf('<option value="%d">%s</option>', $y, $y);
			}
			$field .= '</select>';
			$content['fields'][] = array(
				'title' => $this->_seamless->getText('creditcard_issuedate'),
				'field' => $field
			);
		}

		if($this->_seamless->getConfigValue('creditcard_showissuenumber'))
		{
			$content['fields'][] = array(
				'title' => $this->_seamless->getText('creditcard_issuenumber'),
				'field' => sprintf('<input type="text" class="%s qcs_issuenumber input-text" name="qcs_issuenumber" data-wcs-fieldname="issueNumber" autocomplete="off" value="" maxlength="2"/>',
				                   $cssClass)
			);
		}

		return $content;
	}

}

MainFactory::load_origin_class('qcs_ccard');
