<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;

global $arTheme;
if($arResult['REGIONS']):?>
	<div class="region_wrapper">
		<div class="city_title"><?=Loc::getMessage('CITY_TITLE');?></div>
		<div class="js_city_chooser colored list" data-param-url="<?=urlencode($APPLICATION->GetCurUri());?>" data-param-form_id="city_chooser">
			<span><?=$arResult['CURRENT_REGION']['NAME'];?></span><span class="arrow"><i></i></span>

		</div>
		<div class="dropdown">
			<div class="wrap">
				<?foreach($arResult['REGIONS'] as $id => $arItem):?>
					<div class="more_item <?=($id == $arResult['CURRENT_REGION']['ID'] ? 'current' : '');?>">
						<span data-region_id="<?=$arItem['ID']?>" data-href="<?=$arItem['URL'];?>"><?=$arItem['NAME'];?></span>
					</div>
				<?endforeach;?>
			</div>
		</div>
		<?if($arResult['SHOW_REGION_CONFIRM']):?>
			<div class="confirm_region">
				<?
				$href = 'data-href="'.$arResult['REGIONS'][$arResult['REAL_REGION']['ID']]['URL'].'"';
				if($arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_TYPE']['VALUE'] == 'SUBDOMAIN' && ($arResult['HOST'].$_SERVER['HTTP_HOST'].$arResult['URI'] == $arResult['REGIONS'][$arResult['REAL_REGION']['ID']]['URL']))
				$href = '';?>
				<div class="title"><?=Loc::getMessage('CITY_TITLE');?> <?=$arResult['REAL_REGION']['NAME'];?> ?</div>
				<div class="buttons">
					<span class="btn btn-default aprove" data-id="<?=$arResult['REAL_REGION']['ID'];?>" <?=$href;?>><?=Loc::getMessage('CITY_YES');?></span>
					<span class="btn btn-default white js_city_change"><?=Loc::getMessage('CITY_CHANGE');?></span>
				</div>
			</div>
		<?endif;?>
	</div>
<?endif;?>
