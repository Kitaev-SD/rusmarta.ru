<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
__IncludeLang($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");

$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = array();
if(strlen($notifyOption) > 0)
	$arNotify = unserialize($notifyOption);

if(is_array($arNotify[SITE_ID]) && $arNotify[SITE_ID]['use'] == 'Y' && $USER->IsAuthorized() && is_array($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()]) && !empty($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()])) {?>
	<script type="text/javascript">
		<?foreach($_SESSION["NOTIFY_PRODUCT"][$USER->GetID()] as $val) {?>
			if(BX('url_notify_<?=$val?>'))		
				BX('url_notify_<?=$val?>').innerHTML = "<span class='alertMsg good'><i class='fa fa-check'></i><span class='text'><?=GetMessageJS('MFT_NOTIFY_MESSAGE')?></span></span>";
		<?}?>
	</script>
<?}
echo bitrix_sessid_post();?>