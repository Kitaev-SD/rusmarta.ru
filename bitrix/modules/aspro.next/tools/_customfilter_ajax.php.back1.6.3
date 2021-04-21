<?
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
define('BX_PUBLIC_MODE', 1);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

if (
	!check_bitrix_sessid() ||
	!\Bitrix\Main\Loader::includeModule('iblock') ||
	!\Bitrix\Main\Loader::includeModule('catalog')
)
	return;

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);
$action = $request->get('action');
if($action){
	if($action === 'init' || $action === 'save'){
		require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_js.php');

		AddEventHandler('catalog', 'OnCondCatControlBuildList', array('\Aspro\Next\Property\CustomFilter\CondCtrl', 'GetControlDescr'));

		$ids = $request->get('ids');
		$success = false;

		if(!empty($ids) && is_array($ids)){
			$condTree = new CCatalogCondTree();
			$success = $condTree->Init(
				BT_COND_MODE_DEFAULT,
				BT_COND_BUILD_CATALOG,
				array(
					'FORM_NAME' => $ids['form'],
					'CONT_ID' => $ids['container'],
					'JS_NAME' => $ids['treeObject']
				)
			);
		}

		if($success){
			if($action === 'init'){
				try{
					$condition = \Bitrix\Main\Web\Json::decode($request->get('condition'));
				}
				catch (Exception $e){
					$condition = array();
				}

				$condTree->Show($condition);
			}
			elseif($action === 'save'){
				$result = $condTree->Parse();

				$GLOBALS['APPLICATION']->RestartBuffer();
				echo \Bitrix\Main\Web\Json::encode($result);
			}
		}

		\CMain::FinalActions();
		die();
	}
	else{
		\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

		if($action === 'get_crosssales_iblockprops'){
			$arResult = array();

			if(check_bitrix_sessid() && $request->isPost()){
				$propertyId = (int)$request->get('propertyId');
				$iblockId = (int)$request->get('iblockId');
				if($propertyId > 0){
					$property = \Bitrix\Iblock\PropertyTable::getList(
						array(
							'filter' => array('=ID' => $propertyId),
							'select' => array(
								'ID',
								'PROPERTY_TYPE',
								'USER_TYPE',
								'USER_TYPE_SETTINGS'
							),
						)
					)->fetch();
					if($property){
						$arExcludeUserTypes = \Aspro\Next\Property\CustomFilter\CondCtrl::getCrossSalesExcludePropertyUserTypes();
						$properties = \Bitrix\Iblock\PropertyTable::getList(
							array(
								'filter' => array(
									'=IBLOCK_ID' => $iblockId,
									'PROPERTY_TYPE' => $property['PROPERTY_TYPE'],
								),
								'select' => array(
									'ID',
									'PROPERTY_TYPE',
									'CODE',
									'NAME',
									'USER_TYPE',
									'USER_TYPE_SETTINGS',
								),
							)
						);
						while($arProperty = $properties->fetch()){
							if(in_array($arProperty['USER_TYPE'], $arExcludeUserTypes)){
								continue;
							}

							$arResult[] = array(
								'value' => $arProperty['ID'],
								'label' => \Bitrix\Main\Localization\Loc::getMessage('CUSTOM_FILTER_CONTROL_CROSSALES_PROPERTY_PREFIX').' '.$arProperty['NAME'],
							);
						}
					}
				}
			}

			$GLOBALS['APPLICATION']->RestartBuffer();
			header('Content-Type: application/json');
			echo Bitrix\Main\Web\Json::encode($arResult);
			die();
		}
	}
}
