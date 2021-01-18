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
class qcs_invoice_ORIGIN extends QentaCheckoutSeamless
{
	protected $_defaultSortOrder = 23;
	protected $_paymenttype      = QentaCEE\Stdlib\PaymentTypeAbstract::INVOICE;
	protected $_logoFilename     = 'invoice.png';


	public function __construct()
	{
		parent::__construct();
		$c = strtoupper($this->code);
		define("MODULE_PAYMENT_{$c}_MIN_AMOUNT_TITLE", $this->_seamless->getText('min_amount'));
		define("MODULE_PAYMENT_{$c}_MIN_AMOUNT_DESC", '');
		define("MODULE_PAYMENT_{$c}_MAX_AMOUNT_TITLE", $this->_seamless->getText('max_amount'));
		define("MODULE_PAYMENT_{$c}_MAX_AMOUNT_DESC", '');
		define("MODULE_PAYMENT_{$c}_PROVIDER_TITLE", $this->_seamless->getText('invoiceinstallment_provider'));
		define("MODULE_PAYMENT_{$c}_PROVIDER_DESC", '');
        define("MODULE_PAYMENT_{$c}_EQUAL_ADDRESS_TITLE", $this->_seamless->getText('equal_address'));
        define("MODULE_PAYMENT_{$c}_EQUAL_ADDRESS_DESC", '');
		define("MODULE_PAYMENT_{$c}_CURRENCIES_TITLE", $this->_seamless->getText('currencies'));
		define("MODULE_PAYMENT_{$c}_CURRENCIES_DESC", '');
        define("MODULE_PAYMENT_{$c}_PAYOLUTION_MID_TITLE", $this->_seamless->getText('payolution_mid'));
        define("MODULE_PAYMENT_{$c}_PAYOLUTION_MID_DESC", $this->_seamless->getText('payolution_mid_desc'));
        define("MODULE_PAYMENT_{$c}_PAYOLUTION_CONSENT_TITLE", $this->_seamless->getText('payolution_terms'));
        define("MODULE_PAYMENT_{$c}_PAYOLUTION_CONSENT_DESC", $this->_seamless->getText('payolution_terms_desc'));
	}

    function selection()
    {
        $content = parent::selection();
        if($content === false)
        {
            return false;
        }

        $years = range(date("Y")-10, date("Y")-80);
        $years_options = "";
        foreach ($years as $year) {
            $years_options .= "<option value='$year'>$year</option>";
        }

        $months = array(
            1 => _JANUARY,
            2 => _FEBRUARY,
            3 => _MARCH,
            4 => _APRIL,
            5 => _MAY,
            6 => _JUNE,
            7 => _JULY,
            8 => _AUGUST,
            9 => _SEPTEMBER,
            10 => _OCTOBER,
            11 => _NOVEMBER,
            12 => _DECEMBER
        );
        $months_options = "";

        $days = range(1, 31);
        $days_options = "";
        foreach ($days as $day) {
            $days_options .= "<option value='$day'>$day</option>";
        }

        foreach ($months as $number => $word) {
            $months_options .= "<option value='$number'>$word</option>";
        }

        $script = "<script>
    function qcsInvoiceCheckBirthdate(element) {
        var el = $(element);
        
        var day = (el.attr('name').indexOf(\"_day\")!==-1?el:$('select[name$=_birthdate_day]',el.parent())).val();
        var month = (el.attr('name').indexOf(\"_month\")!==-1?el:$('select[name$=_birthdate_month]',el.parent())).val();
        var year = (el.attr('name').indexOf(\"_year\")!==-1?el:$('select[name$=_birthdate_year]',el.parent())).val();
        
        var dob = new Date();
        dob.setDate(day);
        dob.setMonth(month-1);
        dob.setYear(year);
        dob.setHours(12,0,0,0);
        
        var error = '<div class=\"col-xs-12 qcsAgeError alert alert-danger\" style=\"margin-bottom:0\">" . $this->_seamless->getText('birthdate_too_young') . "</div>';
       
            if (Math.abs(new Date(Date.now() - dob.getTime()).getUTCFullYear() - 1970) < 18) {
                if (el.closest('.form-group').find('.qcsAgeError').length == 0) {
                    el.closest('.form-group').append(error);
                } else {
                    el.closest('.form-group').find('.qcsAgeError').show();
                }
            } else {
                el.closest('.form-group').find('.qcsAgeError').hide();
            }
    }
</script>";


        $field = "$script<div class='form-inline'>
        <select class='qcs_eps input-select form-control' name='qcs_invoice_birthdate_day' onchange='qcsInvoiceCheckBirthdate(this)'>
            $days_options
        </select>
        <select class='qcs_eps input-select form-control' name='qcs_invoice_birthdate_month' onchange='qcsInvoiceCheckBirthdate(this)'>
            $months_options
        </select>
        <select class='qcs_eps input-select form-control' name='qcs_invoice_birthdate_year' onchange='qcsInvoiceCheckBirthdate(this)'>
            $years_options
        </select>
    </div>";

        $field .= '</select>';
        $content['fields'][] = array(
            'title' => $this->_seamless->getText('birthdate'),
            'field' => $field
        );

        if (MODULE_PAYMENT_QCS_INVOICE_PAYOLUTION_CONSENT == "on" && MODULE_PAYMENT_QCS_INVOICE_PROVIDER == 'Payolution') {
            $content['fields'][] = $this->consentCheckbox();
        }
        return $content;
    }

    function consentCheckbox()
    {

        $field = "<input class='form-control' type='checkbox' name='qcs_invoice_payolution_terms' style='height: 13px; width: 13px;'/>";

        $consent_message = preg_replace_callback("/_(.*)_/", function ($matches) {
            if (strlen(MODULE_PAYMENT_QCS_INVOICE_PAYOLUTION_MID)) {
                return "<a style='color:white;mix-blend-mode:difference;' href='https://payment.payolution.com/payolution-payment/infoport/dataprivacyconsent?mId=" . base64_encode(MODULE_PAYMENT_QCS_INVOICE_PAYOLUTION_MID) . "' target='_blank'>$matches[1]</a>";
            } else {
                return $matches[1];
            }
        }, $this->_seamless->getText('consent_text'));

        return array(
            'title' => $consent_message,
            'field' => $field
        );
    }
	/**
	 * @return bool
	 */
	function _preCheck()
	{
		return $this->invoiceInstallmentPreCheck();
	}

    /**
     * check if dob is at least 18 years ago + possible consent check
     */
    public function pre_confirmation_check()
    {
        if ($_POST['payment'] == 'qcs_invoice') {
            $day = $_POST['qcs_invoice_birthdate_day'];
            $month = $_POST['qcs_invoice_birthdate_month'];
            $year = $_POST['qcs_invoice_birthdate_year'];
            $_SESSION['qcs_birthdate'] = $year . '-' . $month . '-' . $day;

            $age = (date("md", date("U", mktime(0, 0, 0, $month, $day, $year))) > date("md")
                ? ((date("Y") - $year) - 1)
                : (date("Y") - $year));

            if ($age < 18) {
                $_SESSION['gm_error_message'] = $this->_seamless->getText('birthdate_too_young');
                xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payment.php');
                die;
            }

            if (MODULE_PAYMENT_QCS_INVOICE_PAYOLUTION_CONSENT == "on" && $_POST['qcs_invoice_payolution_terms'] !== 'on' && MODULE_PAYMENT_QCS_INVOICE_PROVIDER == 'Payolution') {
                $_SESSION['gm_error_message'] = $this->_seamless->getText('payolution_terms_error');
                xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payment.php');
                die;
            }
        }
    }


    /**
	 * module config
	 * @return mixed
	 */
	protected function _configuration()
	{
		$config = parent::_configuration();

		$config['PROVIDER']   = array(
			'configuration_value' => '',
			'set_function'        => "qcs_cfg_pull_down_invoice_provider( "
		);
		$config['CURRENCIES'] = array(
			'configuration_value' => 'EUR'
		);
        $config['EQUAL_ADDRESS'] = array(
            'configuration_value' => 'on',
            'set_function'        => "qcs_cfg_invoice_checkbox("
        );
		$config['MIN_AMOUNT'] = array(
			'configuration_value' => '10'
		);
		$config['MAX_AMOUNT'] = array(
			'configuration_value' => '3500'
		);
        $config['PAYOLUTION_MID'] = array(
            'configuration_value' => ''
        );
        $config['PAYOLUTION_CONSENT'] = array(
            'configuration_value' => 'off',
            'set_function'        => "qcs_cfg_invoice_terms_checkbox("
        );

		return $config;
	}

}

MainFactory::load_origin_class('qcs_invoice');
