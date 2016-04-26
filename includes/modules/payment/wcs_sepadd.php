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

require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/WirecardCheckoutSeamless.php';

/**
 * @see WirecardCheckoutSeamless_ORIGIN
 */
class wcs_sepadd_ORIGIN extends WirecardCheckoutSeamless
{
	protected $_defaultSortOrder = 21;
	protected $_paymenttype      = WirecardCEE_Stdlib_PaymentTypeAbstract::SEPADD;
	protected $_logoFilename     = 'sepa.jpg';


	function selection()
	{
		$content = parent::selection();
		if($content === false)
		{
			return false;
		}

		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_iban'),
			'field' => '<input type="text" class="wcs_sepadd input-text" name="wcs_sepaddiban" data-wcs-fieldname="bankAccountIban" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_bic'),
			'field' => '<input type="text" class="wcs_sepadd input-text wcs_sepaddbic" name="wcs_sepaddbic" data-wcs-fieldname="bankBic" autocomplete="off" value=""/>'
		);
		$content['fields'][] = array(
			'title' => $this->_seamless->getText('sepa_accountowner'),
			'field' => '<input type="text" class="wcs_sepadd input-text" name="wcs_sepaddaccountowner" data-wcs-fieldname="accountOwner" autocomplete="off" value=""/>'
		);

		return $content;
	}

}

MainFactory::load_origin_class('wcs_sepadd');