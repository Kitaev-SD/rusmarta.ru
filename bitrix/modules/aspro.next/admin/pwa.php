<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$moduleClass = 'CNext';
$moduleID = 'aspro.next';
\Bitrix\Main\Loader::includeModule($moduleID);

use Bitrix\Main\Config\Option,
	Bitrix\Main\Localization\Loc,
	Aspro\Next\PWA;

$RIGHT = $APPLICATION->GetGroupRight($moduleID);
?>
<?if($RIGHT >= 'R'):?>
	<?
	$GLOBALS['APPLICATION']->SetAdditionalCss('/bitrix/css/'.$moduleID.'/style.css');
	$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage('ASPRO_NEXT_PAGE_TITLE'));

	$arSites = array();
	$db_res = CSite::GetList(($by = 'id'), ($sort = 'asc'), array('ACTIVE' => 'Y'));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}

	$arTabs = array();
	foreach($arSites as $key => $arSite){
		$optionsSiteID = $arSite['ID'];
		$siteDir = rtrim('https://'.preg_replace('/\/+/', '/', $arSite['SERVER_NAME'].'/'.$arSite['DIR'].'/'), '/');

		$arTabs[] = array(
			'DIV' => 'edit'.($key + 1),
			'TAB' => GetMessage('MAIN_OPTIONS_SITE_TITLE', array('#SITE_NAME#' => $arSite['NAME'], '#SITE_ID#' => $arSite['ID'])),
			// "TITLE" => GetMessage("MAIN_OPTIONS_TITLE"),
			'ICON' => 'settings',
			'PAGE_TYPE' => 'site_settings',
			'SITE_ID' => $arSite['ID'],
			'SITE_DIR' => $arSite['DIR'],
			'OPTIONS' => PWA::getValues($optionsSiteID),
		);
	}

	$tabControl = new CAdminTabControl('tabControl', $arTabs);

	if($REQUEST_METHOD === 'POST' && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT >= 'W' && check_bitrix_sessid()){
		global $APPLICATION, $CACHE_MANAGER;

		if(strlen($RestoreDefaults) > 0){

		}
		else{
			foreach($arTabs as $key => $arTab){
				$optionsSiteID = $arTab['SITE_ID'];

				foreach(PWA::getParams() as $blockCode => $arBlock){
					if(in_array($blockCode,$arHideProps)) continue;
					foreach($arBlock['OPTIONS'] as $optionCode => $arOption){
						$optionType = $arOption['TYPE'];
						$optionTypeExt = array_key_exists('TYPE_EXT', $arOption) ? $arOption['TYPE_EXT'] : false;

						if($optionTypeExt === 'colorpicker'){
							$moduleClass::checkColor($_REQUEST[$optionCode.'_'.$optionsSiteID]);
						}

						$newVal = $_REQUEST[$optionCode.'_'.$optionsSiteID];

						if($optionType === 'checkbox'){
							if(!strlen($newVal) || $newVal != 'Y'){
								$newVal = 'N';
							}
						}
						elseif($optionType === 'file'){
							$arValueDefault = serialize(array());
							$newVal = unserialize(Option::get($moduleID, $optionCode, $arValueDefault, $optionsSiteID));
							if(isset($_REQUEST[$optionCode.'_'.$optionsSiteID.'_del']) || (isset($_FILES[$optionCode.'_'.$optionsSiteID]) && strlen($_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['0']))){
								$arValues = $newVal;
								$arValues = (array)$arValues;
								foreach($arValues as $fileID){
									CFile::Delete($fileID);
								}
								$newVal = serialize(array());
							}

							if(isset($_FILES[$optionCode.'_'.$optionsSiteID]) && (strlen($_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['n0']) || strlen($_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['0']))){
								$arValues = array();
								$absFilePath = (strlen($_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['n0']) ? $_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['n0'] : $_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['0']);
								$arOriginalName = (strlen($_FILES[$optionCode.'_'.$optionsSiteID]['name']['n0']) ? $_FILES[$optionCode.'_'.$optionsSiteID]['name']['n0'] : $_FILES[$optionCode.'_'.$optionsSiteID]['name']['0']);
								if(file_exists($absFilePath)){
									$arFile = CFile::MakeFileArray($absFilePath);
									$arFile['name'] = $arOriginalName; // for original file extension

									if($bIsIco = strpos($arOriginalName, '.ico') !== false){
										$script_files = COption::GetOptionString('fileman', '~script_files', 'php,php3,php4,php5,php6,phtml,pl,asp,aspx,cgi,dll,exe,ico,shtm,shtml,fcg,fcgi,fpl,asmx,pht,py,psp,var');
										$arScriptFiles = explode(',', $script_files);
										if(($p = array_search('ico', $arScriptFiles)) !== false){
											unset($arScriptFiles[$p]);
										}

										$tmp = implode(',', $arScriptFiles);
										Option::set('fileman', '~script_files', $tmp);
									}

									if($fileID = CFile::SaveFile($arFile, $moduleClass)){
										$arValues[] = $fileID;
									}

									if($bIsIco){
										Option::set('fileman', '~script_files', $script_files);
									}
								}
								$newVal = serialize($arValues);
							}

							if(!isset($_FILES[$optionCode.'_'.$optionsSiteID]) || (!strlen($_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['n0']) && !strlen($_FILES[$optionCode.'_'.$optionsSiteID]['tmp_name']['0']) && !isset($_REQUEST[$optionCode.'_'.$optionsSiteID.'_del']))){
							}

							if(is_array($newVal)){
								$newVal = serialize($newVal);
							}

							Option::set($moduleID, $optionCode, $newVal, $optionsSiteID);

							unset($arTab['OPTIONS'][$optionCode]);
						}
						elseif($optionType === 'multiselectbox'){
							$newVal = implode(',', $newVal);
						}

						if($optionType !== 'file'){
							$arTab['OPTIONS'][$optionCode] = $newVal;
						}

						Option::set($moduleID, $optionCode, $newVal, $optionsSiteID);
					}
				}

				$arTabs[$key] = $arTab;

				// generate manifest
				PWA::generate($optionsSiteID);
			}
		}

		// clear composite cache
		if($compositeMode = $moduleClass::IsCompositeEnabled()){
			$obCache = new CPHPCache();
			$obCache->CleanDir('', 'html_pages');
			$moduleClass::EnableComposite($compositeMode === 'AUTO_COMPOSITE');
		}

		$APPLICATION->RestartBuffer();
	}

	CJSCore::Init(array('jquery'));
	CAjax::Init();
	?>
	<?if(!count($arTabs)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage('ASPRO_NEXT_NO_SITE_INSTALLED', array('#SESSION_ID#' => bitrix_sessid_get()))?></div>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?else:?>
		<?$tabControl->Begin();?>
		<form method="post" class="next_options" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
			<?=bitrix_sessid_post();?>
			<?foreach($arTabs as $key => $arTab):?>
				<?$tabControl->BeginNextTab();?>
				<?if($arTab['SITE_ID']):?>
					<?$optionsSiteID = $arTab['SITE_ID'];?>
					<?foreach(PWA::getParams() as $blockCode => $arBlock):?>
						<tr class="heading"><td colspan="2"><?=$arBlock['TITLE']?></td></tr>
						<?foreach($arBlock['OPTIONS'] as $optionCode => $arOption):?>
							<?if(array_key_exists($optionCode, $arTab['OPTIONS']) || $arOption['TYPE'] == 'note'):?>
								<?if($arOption['TYPE'] === 'note'):?>
									<?
									if($optionCode === 'CONTACTS_EDIT_LINK_NOTE'){
										$contactsHref = str_replace('//', '/', $arTab['SITE_DIR'].'/contacts/?bitrix_include_areas=Y');
										$arOption['TITLE'] = GetMessage('CONTACTS_OPTIONS_EDIT_LINK_NOTE', array('#CONTACTS_HREF#' => $contactsHref));
									}
									?>
									<tr data-option_code="<?=$optionCode;?>">
										<td colspan="2" align="center">
											<?=BeginNote('align="center" name="'.htmlspecialcharsbx($optionCode).'_'.$optionsSiteID.'"');?>
											<?=$arOption['TITLE']?>
											<?=EndNote();?>
										</td>
									</tr>
								<?else:?>
									<tr data-optioncode="<?=$optionCode;?>">
										<?=$moduleClass::ShowAdminRow($optionCode, $arOption, $arTab, array());?>
									</tr>
								<?endif;?>
							<?endif;?>
						<?endforeach;?>
					<?endforeach;?>
				<?endif;?>
			<?endforeach;?>
			<?if($REQUEST_METHOD === 'POST' && strlen($Update.$Apply.$RestoreDefaults) && check_bitrix_sessid()):?>
				<?if(strlen($Update) && strlen($_REQUEST['back_url_settings'])):?>
					<?LocalRedirect($_REQUEST['back_url_settings']);?>
				<?else:?>
					<?LocalRedirect($APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings'])."&".$tabControl->ActiveTabParam());?>
				<?endif;?>
			<?endif;?>
			<?$tabControl->Buttons();?>
			<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage('MAIN_OPT_APPLY')?>" title="<?=GetMessage('MAIN_OPT_APPLY_TITLE')?>">
			<?if(strlen($_REQUEST['back_url_settings']) > 0): ?>
				<input type="button" name="Cancel" value="<?=GetMessage('MAIN_OPT_CANCEL')?>" title="<?=GetMessage('MAIN_OPT_CANCEL_TITLE')?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST['back_url_settings']))?>'">
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST['back_url_settings'])?>">
			<?endif;?>
		</form>
		<?$tabControl->End();?>
	<?endif;?>
<?else:?>
	<?=CAdminMessage::ShowMessage(GetMessage('ASPRO_NEXT_NO_RIGHTS_FOR_VIEWING'));?>
<?endif;?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>