<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

if(strlen($arResult["ERROR_MESSAGE"]) > 0)
	ShowError($arResult["ERROR_MESSAGE"]);

if(count($arResult["PROFILES"])) {
	$order = isset($_REQUEST["order"]) && $_REQUEST["order"] == "asc" ? "desc" : "asc";?>
	<div class="cart-items">
		<div class="equipment-profile">
			<div class="thead">
				<div class="cart-item-number">
					<a href="<?=$APPLICATION->GetCurPageParam("by=ID&amp;order=".$order."#nav_start", array("by", "order"))?>"><?=Loc::getMessage("STPPL_ID")?></a>
				</div>
				<div class="cart-item-date">
					<a href="<?=$APPLICATION->GetCurPageParam("by=DATE_UPDATE&amp;order=".$order."#nav_start", array("by", "order"))?>"><?=Loc::getMessage("STPPL_DATE_UPDATE")?></a>
				</div>
				<div class="cart-item-name">
					<a href="<?=$APPLICATION->GetCurPageParam("by=NAME&amp;order=".$order."#nav_start", array("by", "order"))?>"><?=Loc::getMessage("STPPL_NAME")?></a>
				</div>
				<div class="cart-item-person-type">
					<a href="<?=$APPLICATION->GetCurPageParam("by=PERSON_TYPE_ID&amp;order=".$order."#nav_start", array("by", "order"))?>"><?=Loc::getMessage("STPPL_PERSON_TYPE_ID")?></a>
				</div>
				<div class="cart-item-actions"></div>
			</div>
			<div class="tbody">
				<?foreach($arResult["PROFILES"] as $val) {?>
					<div class="tr">
						<div class="tr_into">
							<div class="cart-item-number"><?=$val["ID"]?></div>
							<div class="cart-item-date"><?=$val["DATE_UPDATE"]?></div>
							<div class="cart-item-name">
								<a href="<?=$val['URL_TO_DETAIL']?>"><?=$val["NAME"]?></a>
							</div>
							<div class="cart-item-person-type"><?=$val["PERSON_TYPE"]["NAME"]?></div>
							<div class="cart-item-actions">
								<div class="delete">
									<a class="deleteitem" title="<?=Loc::getMessage('STPPL_DELETE_DESCR')?>" href="javascript:if(confirm('<?= Loc::getMessage('STPPL_DELETE_CONFIRM')?>')) window.location='<?=$val['URL_TO_DETELE']?>'"><i class="fa fa-trash-o"></i></a>
								</div>
							</div>
						</div>
					</div>
				<?}?>				
			</div>
		</div>
	</div>
	<?if(strlen($arResult["NAV_STRING"]) > 0)
		echo $arResult["NAV_STRING"];
} else {
	ShowNote(Loc::getMessage("STPPL_EMPTY_PROFILE_LIST"), "infotext");
}?>