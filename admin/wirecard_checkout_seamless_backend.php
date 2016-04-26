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

/** @var GMWirecardCheckoutSeamless_ORIGIN $wcs */
$wcs = MainFactory::create_object('GMWirecardCheckoutSeamless');

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
	header('403 Forbidden', true, 403);
	xtc_db_close();
	die;
}

if(!isset($_POST['operation']) || !isset($_POST['payment']))
{
	header('403 Forbidden', true, 403);
	xtc_db_close();
	die;
}

$payment = json_decode(stripslashes($_POST['payment']));
if(!is_object($payment))
{
	xtc_db_close();
	header('403 Forbidden', true, 403);
	die;
}

foreach($payment as $p => $v)
{
	$payment->$p = filter_var($v, FILTER_SANITIZE_FULL_SPECIAL_CHARS,
	                          FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_ENCODE_AMP);
}

$wcs->log(print_r($_POST, true));

switch($_POST['operation'])
{
	case 'deposit':
		if(!isset($_POST['amount']) || $_POST['amount'] <= 0)
		{
			$_SESSION['wcs_messages'] = Array($wcs->getText('amount_invalid'));
			xtc_db_close();
			die;
		}
		$ret = $wcs->deposit($payment->orderNumber, $_POST['amount'], $payment->currency);
		break;

	case 'depositreversal':
		$ret = $wcs->depositReversal($payment->orderNumber, $payment->paymentNumber);
		break;

	case 'approvereversal':
		$ret = $wcs->approveReversal($payment->orderNumber);
		break;

	case 'refund':
		if(!isset($_POST['amount']) || $_POST['amount'] <= 0)
		{
			$_SESSION['wcs_messages'] = Array($wcs->getText('amount_invalid'));
			xtc_db_close();
			die;
		}

		$ret = $wcs->refund($payment->orderNumber, $_POST['amount'], $payment->currency);
		break;

	case 'refundreversal':
		$ret = $wcs->refundReversal($payment->orderNumber, $payment->creditNumber);
		break;

	case 'transferfund':

		try
		{
			$client = $wcs->transferfund($_POST['transfertype']);

			if(strlen($payment->orderNumber))
			{
				$client->setOrderNumber($payment->orderNumber);
			}
			if(strlen($payment->orderReference))
			{
				$client->setOrderReference($payment->orderReference);
			}
			if(strlen($payment->creditNumber))
			{
				$client->setCreditNumber($payment->creditNumber);
			}

			switch($_POST['transfertype'])
			{
				case WirecardCEE_QMore_BackendClient::$TRANSFER_FUND_TYPE_EXISTING:
					/** @var WirecardCEE_QMore_Request_Backend_TransferFund_Existing $client */
					if(strlen($payment->customerStatement))
					{
						$client->setCustomerStatement($payment->customerStatement);
					}

					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription,
					                     $payment->sourceOrderNumber);
					break;

				case WirecardCEE_QMore_BackendClient::$TRANSFER_FUND_TYPE_SKIRLLWALLET:
					/** @var WirecardCEE_QMore_Request_Backend_TransferFund_SkrillWallet $client */
					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription,
					                     $payment->customerStatement, $payment->consumerEmail);
					break;

				case WirecardCEE_QMore_BackendClient::$TRANSFER_FUND_TYPE_MONETA:
					/** @var WirecardCEE_QMore_Request_Backend_TransferFund_Moneta $client */
					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription,
					                     $payment->customerStatement, $payment->consumerWalletId);
					break;

				case WirecardCEE_QMore_BackendClient::$TRANSFER_FUND_TYPE_SEPACT:
					/** @var WirecardCEE_QMore_Request_Backend_TransferFund_SepaCT $client */
					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription, $payment->bankAccountOwner,
					                     $payment->bankBic, $payment->bankAccountIban);
					break;

			}
		}
		catch(Exception $e)
		{
			header('400 Bad Request', true, 400);
			print json_encode(array($e->getMessage()));
			xtc_db_close();
			die;
		}

		break;
}

/** @var WirecardCEE_QMore_Response_Backend_ResponseAbstract $ret */
if($ret->getNumberOfErrors() > 0)
{
	header('400 Bad Request', true, 400);
	$errors = Array();
	foreach($ret->getErrors() as $e)
	{
		if (strlen($e->getConsumerMessage()))
			$errors[] = html_entity_decode($e->getConsumerMessage());
		else
			$errors[] = html_entity_decode($e->getMessage());
	}
	$_SESSION['wcs_messages'] = $errors;
	print json_encode($errors);
}
else
{
	print json_encode($ret->getResponse());
}

require_once 'includes/application_bottom.php';