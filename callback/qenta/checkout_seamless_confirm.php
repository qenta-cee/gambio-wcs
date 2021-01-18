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

$seamless = new GMQentaCheckoutSeamless_ORIGIN();
print $seamless->handleConfirm($_POST);

