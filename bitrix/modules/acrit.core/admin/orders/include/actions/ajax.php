<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("acrit.core");

use Acrit\Core\Orders\Controller,
	Acrit\Core\Orders\Rest,
	Acrit\Core\Orders\OrdersInfo;

Controller::setModuleId($strModuleId);

$action = trim($_REQUEST['action'] ?? '');
$params = $_REQUEST['params'];
$result = [];
$result['status'] = 'error';
$result['log'] = [];

switch ($action) {
	// Reset connection
	case 'options_connect_reset':
		//// Reset placements and event handlers
		//$sync_active = Settings::get('active');
		//if ($sync_active) {
		//	Controller::removePortalPlacements();
		//	Controller::unregCrmHandlers();
		//}
		// Reset connection
		Rest::saveAuthInfo('');
		$result['status'] = 'ok';
		break;
	// Users search
	case 'find_users':
		$result = [];
		if (strlen($_REQUEST['q']) > 3) {
			$list = OrdersInfo::getUsers($_REQUEST['q']);
			foreach ($list as $item) {
				$result[$item['id']] = $item['name'] . ', ' . $item['code'] . ' [' . $item['id'] . ']';
			}
		}
		break;
}

echo \Bitrix\Main\Web\Json::encode($result);
