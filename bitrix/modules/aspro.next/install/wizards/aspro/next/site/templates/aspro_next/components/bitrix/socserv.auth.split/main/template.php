<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?
if($arResult['ERROR_MESSAGE'])
	ShowMessage($arResult['ERROR_MESSAGE']);
?>
<script type="text/javascript">
	function ShowTwitDiv(){
		var obTwitterRecipients = document.getElementById('soc-serv-recipients');
		if(obTwitterRecipients.style.display == 'block')
			obTwitterRecipients.style.display = 'none';
		else
			obTwitterRecipients.style.display = 'block'
	}
</script>
<?
$arServices = $arResult["AUTH_SERVICES_ICONS"];
$userIdTwitter = array();
$userIdOther = array();
$showDivTwitter = false;
$arPerm = array();
?>
<div class="soc-avt bottom-form">
	<div class="big-title">
		<?=GetMessage("SS_GET_COMPONENT_INFO")?>
	</div>
	<?if(!empty($arResult["AUTH_SERVICES"])):?>
		<?
		$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "main",
			array(
				"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
				"CURRENT_SERVICE"=>$arResult["CURRENT_SERVICE"],
				"AUTH_URL"=>$arResult['CURRENTURL'],
				"POST"=>$arResult["POST"],
				"SHOW_TITLES"=>'N',
				"FOR_SPLIT"=>'Y',
				"AUTH_LINE"=>'N',
			),
			$component,
			array("HIDE_ICONS"=>"Y")
		);
		?>
	<?endif;?>
	<?if(isset($arResult["DB_SOCSERV_USER"]) && $arParams["SHOW_PROFILES"] != 'N'):?>
		<div class="soc-serv-my-actives">
			<input type="hidden" name="bEdit" value="N" />
		</div>
		<div class="title middle" id="soc-serv-title-id">
			<?=GetMessage("SS_YOUR_ACCOUNTS");?>
		</div>
		<div class="soc-serv-accounts">
			<table cellspacing="0" cellpadding="0">
				<?
				foreach($arResult["DB_SOCSERV_USER"] as $key => $arUser)
				{
					if(!$icon = htmlspecialcharsbx($arResult["AUTH_SERVICES_ICONS"][$arUser["EXTERNAL_AUTH_ID"]]["ICON"]))
						$icon = 'openid';
					$authID = ($arServices[$arUser["EXTERNAL_AUTH_ID"]]["NAME"]) ? $arServices[$arUser["EXTERNAL_AUTH_ID"]]["NAME"] : $arUser["EXTERNAL_AUTH_ID"];
					if($arUser["EXTERNAL_AUTH_ID"] == "Twitter")
					{
						$showDivTwitter = true;
						$userIdTwitter[] = $arUser["ID"];
						$userPerm = $arUser["PERMISSIONS"];
					}
					else
						$userIdOther[] = $arUser["ID"];
					?>
					<tr class="soc-serv-personal">
						<td class="bx-ss-icons">
							<div class="icon" title="<?=$authID?>">
								<?if ($arUser["PERSONAL_LINK"] != ''):?>
									<a class="soc-serv-link" target="_blank" href="<?=$arUser["PERSONAL_LINK"]?>">
								<?endif;?>
								<i class="bx-ss-icon <?=$icon?>">&nbsp;</i>
								<?if ($arUser["PERSONAL_LINK"] != ''):?>
									</a>
								<?endif;?>
							</div>
						</td>
						<td class="soc-serv-name">
							<div class="auth-soc-title"><?=$authID?></div>
							<div class="auth-soc-name">
								<?$userID = $GLOBALS["USER"]->GetID();?>
								<?\Bitrix\Main\Loader::includeModule('socialservices');?>
								<?$arTmpUser = CUser::GetList(($by="id"), ($order="asc"), array("ID" => $userID), array("FIELDS" => array("ID", "PERSONAL_PHONE", "PASSWORD", "PERSONAL_PHOTO"), "SELECT" => array("UF_".strtoupper($arUser['EXTERNAL_AUTH_ID']))))->Fetch();
								$arSocUser = CSocServAuthDB::GetList(array(), array('USER_ID' => $userID))->Fetch();?>
								<?if($arSocUser && ($arSocUser['PERSONAL_PHOTO'] && $arSocUser['PERSONAL_PHOTO'] > 1)):?>
									<i class="soc-serv-photo">
										<?echo CFile::ShowImage($arSocUser['PERSONAL_PHOTO'], 30, 30, "border=0", "", true);?>
									</i>
								<?elseif(intval($arTmpUser["UF_".strtoupper($arUser['EXTERNAL_AUTH_ID'])]) > 0):?>
									<i class="soc-serv-photo">
										<?echo CFile::ShowImage($arTmpUser["UF_".strtoupper($arUser['EXTERNAL_AUTH_ID'])], 30, 30, "border=0", "", true);?>
									</i>
								<?elseif($arSocUser && $arTmpUser['PERSONAL_PHOTO']):?>
									<i class="soc-serv-photo">
										<?echo CFile::ShowImage($arTmpUser['PERSONAL_PHOTO'], 30, 30, "border=0", "", true);?>
									</i>
								<?endif;?>
								<i class="soc-serv-text"><?=$arUser["VIEW_NAME"]?></i>
							</div>
						</td>
						<td class="split-item-actions">
							<?if (in_array($arUser["ID"], $arResult["ALLOW_DELETE_ID"])):?>
							<a class="btn transparent remove-soc btn-sm" href="?action=delete&user_id=<?=$arUser["ID"]."&".bitrix_sessid_get()?>" onclick="return confirm('<?=GetMessage("SS_PROFILE_DELETE_CONFIRM")?>')" title=<?=GetMessage("SS_DELETE")?>><?=GetMessage("SS_DELETE")?></a>
							<?endif;?>
						</td>
					</tr>
					<?
				}
				?>

			</table>
		</div>
		<?if($showDivTwitter):?>
			<div class="soc-serv-title-grey">
				<?if(COption::GetOptionString("socialservices", "get_message_from_twitter", "N") == 'Y'):?>
				<br><?=str_replace("#hash#", $arResult["TWIT_HASH"], GetMessage("SS_SEND_MESSAGE_TO"))."  "?><a href="javascript:void(0)" onclick="ShowTwitDiv()"><?=GetMessage("SS_TO_RECIPIENTS")?></a>
					<div id="soc-serv-recipients">
				<?
				$APPLICATION->IncludeComponent(
					"bitrix:main.post.form",
					"",
					$formParams = Array(
						"FORM_ID" => "bx_user_profile_form",
						"SHOW_MORE" => "Y",
						"PARSER" => Array("Bold", "Italic", "Underline", "Strike", "ForeColor",
							"FontList", "FontSizeList", "RemoveFormat", "Quote", "Code",
							"MentionUser",
						),
						"BUTTONS" => Array(
							"MentionUser",
						),
						"DESTINATION" => array(
							"VALUE" => $arResult["PostToShow"]["FEED_DESTINATION"],
							"SHOW" => "Y"
						),
					),
					false,
					Array("HIDE_ICONS" => "Y")
				);
				?>
					</div>
				<?endif;?>
			</div>
		<?endif;?>
	<?endif;?>
</div>
<?//endif;?>

<?if(!empty($userIdTwitter))
{
	foreach($userIdTwitter as $value)
	{
	?>
		<input type="hidden" name="USER_ID_TWITTER[<?=$value?>]" value="<?=$value?>" />
	<?
	}
}
if(!empty($userIdOther))
{
	foreach($userIdOther as $value)
	{
	?>
		<input type="hidden" name="USER_ID_OTHER[<?=$value?>]" value="<?=$value?>" />
	<?
	}
}?>