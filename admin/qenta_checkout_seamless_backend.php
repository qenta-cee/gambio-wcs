<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

require_once 'includes/application_top.php';

/** @var GMQentaCheckoutSeamless_ORIGIN $qcs */
$qcs = MainFactory::create_object('GMQentaCheckoutSeamless');

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

$qcs->log(print_r($_POST, true));

switch($_POST['operation'])
{
	case 'deposit':
		if(!isset($_POST['amount']) || $_POST['amount'] <= 0)
		{
			$_SESSION['qcs_messages'] = Array($qcs->getText('amount_invalid'));
			xtc_db_close();
			die;
		}
		$ret = $qcs->deposit($payment->orderNumber, $_POST['amount'], $payment->currency);
		break;

	case 'depositreversal':
		$ret = $qcs->depositReversal($payment->orderNumber, $payment->paymentNumber);
		break;

	case 'approvereversal':
		$ret = $qcs->approveReversal($payment->orderNumber);
		break;

	case 'refund':
		if(!isset($_POST['amount']) || $_POST['amount'] <= 0)
		{
			$_SESSION['qcs_messages'] = Array($qcs->getText('amount_invalid'));
			xtc_db_close();
			die;
		}

		$ret = $qcs->refund($payment->orderNumber, $_POST['amount'], $payment->currency);
		break;

	case 'refundreversal':
		$ret = $qcs->refundReversal($payment->orderNumber, $payment->creditNumber);
		break;

	case 'transferfund':

		try
		{
			$client = $qcs->transferfund($_POST['transfertype']);

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
				case QentaCEE\QMore\BackendClient::$TRANSFER_FUND_TYPE_EXISTING:
					/** @var QentaCEE\QMore\Request\Backend\TransferFund $client */
					if(strlen($payment->customerStatement))
					{
						$client->setCustomerStatement($payment->customerStatement);
					}

					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription,
					                     $payment->sourceOrderNumber);
					break;

				case QentaCEE\QMore\BackendClient::$TRANSFER_FUND_TYPE_SKIRLLWALLET:
					/** @var QentaCEE\QMore\Request\Backend\TransferFund\SkrillWallet $client */
					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription,
					                     $payment->customerStatement, $payment->consumerEmail);
					break;

				case QentaCEE\QMore\BackendClient::$TRANSFER_FUND_TYPE_MONETA:
					/** @var QentaCEE\QMore\Request\Backend\TransferFund\Moneta $client */
					$ret = $client->send($payment->amount, $payment->currency, $payment->orderDescription,
					                     $payment->customerStatement, $payment->consumerWalletId);
					break;

				case QentaCEE\QMore\BackendClient::$TRANSFER_FUND_TYPE_SEPACT:
					/** @var QentaCEE\QMore\Request\Backend\TransferFund\SepaCT $client */
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

/** @var QentaCEE\QMore\Response\Backend\ResponseAbstract $ret */
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
	$_SESSION['qcs_messages'] = $errors;
	print json_encode($errors);
}
else
{
	print json_encode($ret->getResponse());
}

require_once 'includes/application_bottom.php';