<?php
/**
 * Shop System Plugins
 * - Terms of use can be found under
 * https://guides.qenta.com/shop_plugins:info
 * - License can be found under:
 * https://github.com/qenta-cee/gambio-qcs/blob/master/LICENSE
*/

chdir('../../');
require_once('includes/application_top.php');
require_once DIR_FS_DOCUMENT_ROOT . 'includes/classes/QentaCheckoutSeamless.php';

$response = $_POST['response'];

xtc_db_close();
?>
<!DOCTYPE HTML>
<html>
	<!-- DS Store Return -->
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript">
			function setResponse(response) {
				if (typeof parent.WirecardCEE_Fallback_Request_Object == 'object') {
					parent.WirecardCEE_Fallback_Request_Object.setResponseText(response);
				}
				else {
					console.log('Not a valid seamless fallback call.');
				}
			}
		</script>
	</head>

	<body onload='setResponse("<?php echo $response ?>");'>

	</body>
</html>
