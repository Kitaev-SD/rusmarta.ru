<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
global $USER;
$bSuccessConfigSave = false;

if($USER->IsAdmin() && (isset($_POST['SAVE_OPTIONS']) && $_POST['SAVE_OPTIONS'] == 'Y'))
{
	if(isset($_SESSION['THEME']) && $_SESSION['THEME'])
	{
		if($_SESSION['THEME'][SITE_ID])
		{
			\Bitrix\Main\Loader::includeModule('aspro.next');

			// get options
			foreach(CNext::$arParametrsList as $blockCode => $arBlock)
			{
				if($arBlock['OPTIONS'] && is_array($arBlock['OPTIONS']))
				{
					foreach($arBlock['OPTIONS'] as $optionCode => $arOption)
					{
						if($arOption['TYPE'] !== 'note' && $arOption['TYPE'] !== 'includefile' && $arOption['TYPE'] !== 'file')
							$arBackParametrs[$optionCode] = $arOption;
					}
				}
			}
			$bSuccessConfigSave = true;
			$arDependentParams = array();
			foreach($_SESSION['THEME'][SITE_ID] as $optionCode => $optionValue)
			{
				if($arBackParametrs[$optionCode]) //save exists option
				{
					\Bitrix\Main\Config\Option::set('aspro.next', $optionCode, $optionValue, SITE_ID);
				}
				else //get dependent option
				{
					if(strpos($optionCode, 'index') !== false)
					{
						$arTmpOption = explode('_', $optionCode, 2);
						$index_code = reset($arTmpOption);
						$index_subvalue = end($arTmpOption);

						$arDependentParams[$index_code][$index_subvalue] = $optionValue;
					}
					else
						$arDependentParams[$optionCode] = $optionValue;
				}
			}
			if($arDependentParams) // save dependent options
			{
				foreach($arBackParametrs as $optionCode => $arOption)
				{
					if(isset($arOption['DEPENDENT_PARAMS']) && $arOption['DEPENDENT_PARAMS'])
					{
						foreach($arOption['DEPENDENT_PARAMS'] as $dependOptionCode => $arValue)
						{
							if($arDependentParams[$dependOptionCode])
							{
								if($arValue['LIST'][$arDependentParams[$dependOptionCode]])
									\Bitrix\Main\Config\Option::set('aspro.next', $dependOptionCode, $arDependentParams[$dependOptionCode], SITE_ID);
							}
						}
					}
					elseif(isset($arOption['SUB_PARAMS']) && $arOption['SUB_PARAMS'])
					{
						$arOptionKeys = array_keys($arDependentParams);
						foreach($arDependentParams as $key => $arSubParams)
						{
							if($arOption['LIST'][$key] && $arOption['SUB_PARAMS'][$key])
							{
								\Bitrix\Main\Config\Option::set('aspro.next', "NESTED_OPTIONS_".$optionCode."_".$key, serialize($arSubParams), SITE_ID);
							}							
						}
					}
				}
			}
		}
	}
}

if($bSuccessConfigSave)
	$addResult = array('STATUS' => 'OK', 'MESSAGE' => 'CONFIG_SAVE_SUCCESS');
else
	$addResult = array('STATUS' => 'ERROR', 'MESSAGE' => 'CONFIG_SAVE_FAIL');	

echo json_encode($addResult);
die();
?>