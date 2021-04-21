<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<? /* øàáëîí edost - ÍÀ×ÀËÎ */ ?>
<? if (isset($arResult['edost']['format'])) { ?>

<script type="text/javascript">
	function changePaySystem(param) {
		if (BX("account_only") && BX("PAY_CURRENT_ACCOUNT"))
			if (BX("account_only").value == 'Y') {
				if (param == 'account') {
					if (BX("PAY_CURRENT_ACCOUNT").checked) BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
					else BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");

					var el = document.getElementsByName("PAY_SYSTEM_ID");
					for(var i = 0; i < el.length; i++) el[i].checked = false;
				}
				else {
					BX("PAY_CURRENT_ACCOUNT").checked = false;
					BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
				}
			}
			else if (BX("account_only").value == 'N') {
				if (param == 'account') {
					if (BX("PAY_CURRENT_ACCOUNT").checked) BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
					else BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
				}
			}

		submitForm();
	}
</script>

<? if ((!empty($arResult['PAY_SYSTEM']) || $arResult['PAY_FROM_ACCOUNT'] == 'Y') && !empty($arResult['edost']['format']['active']['id'])) { ?>
<?=(!empty($arResult['edost']['format']['active']['cod_tariff']) ? '<div style="height: 10px;"></div>' : '')?>
<div class="edost edost_main edost_template_div<?=(isset($resize['delimiter']) ? $resize['delimiter'] : ' edost_delimiter_normal')?>"<?=(!empty($arResult['edost']['format']['active']['cod_tariff']) ? ' style="display: none;"' : '')?>>
<?
	if (!isset($table_width)) $table_width = 645;
	$hide_radio = (count($arResult['PAY_SYSTEM']) == 1 && $arResult['PAY_FROM_ACCOUNT'] != 'Y' ? true : false);
	$ico_default = $templateFolder.'/images/logo-default-ps.gif';
?>
	<h4><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></h4>

	<div style="max-width: <?=$table_width?>px;">
<?
	$i2 = 0;
	if ($arResult['PAY_FROM_ACCOUNT'] == 'Y') {
		$i2++;
		$id = 'PAY_CURRENT_ACCOUNT';
		$accountOnly = ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y') ? 'Y' : 'N';
?>
		<input type="hidden" id="account_only" value="<?=$accountOnly?>">
		<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="edost_resize_ico<?=(isset($resize['ico'][0]) ? $resize['ico'][0] : ' edost_ico_normal')?>" width="95">
					<input class="edost_format_radio" type="checkbox" name="<?=$id?>" id="<?=$id?>" value="Y" <?=($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y' ? 'checked="checked"' : '')?> onclick="changePaySystem('account');">

<?					if (!empty($ico_default)) { ?>
					<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico" src="<?=$ico_default?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico"></div>
<?					} ?>
				</td>
				<td class="edost_format_tariff">
					<label for="<?=$id?>">
					<span class="edost_format_tariff"><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT')?></span>

					<div class="edost_format_description edost_description"><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT1').' <b>'.$arResult['CURRENT_BUDGET_FORMATED']?></b></div>
					<div class="edost_format_description edost_description">
						<?=($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' ? GetMessage('SOA_TEMPL_PAY_ACCOUNT3') : GetMessage('SOA_TEMPL_PAY_ACCOUNT2'))?>
					</div>
					</label>
				</td>
			</tr>
		</table>
<?	}

	foreach($arResult['PAY_SYSTEM'] as $v) {
		if ($i2 != 0) {
			echo '<div class="edost_resize_delimiter edost_delimiter edost_delimiter_ms"></div>';
			echo '<div class="edost_resize_delimiter2 edost_delimiter edost_delimiter_mb2"></div>';
		}
		$i2++;

		$id = 'ID_PAY_SYSTEM_ID_'.$v['ID'];
		$value = $v['ID'];
		$checked = ($v['CHECKED'] == 'Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y') ? true : false);

		if (!empty($v['PSA_LOGOTIP']['SRC'])) $ico = $v['PSA_LOGOTIP']['SRC'];
		else if (!empty($ico_default)) $ico = $ico_default;
		else $ico = false;

		$ico_width = ($hide_radio ? '70' : '95');

		$row = (isset($resize['ico_row']) ? $resize['ico_row'] : 3);
		if ($row == 'auto') $row = 1;
?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="edost_resize_ico<?=(isset($resize['ico'][0]) ? $resize['ico'][0] : ' edost_ico_normal')?>" width="<?=($ico_width - (isset($resize['ico'][1]) ? $resize['ico'][1] : 0))?>" data-width="<?=$ico_width?>" rowspan="<?=$row?>">
					<input class="edost_format_radio" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="PAY_SYSTEM_ID" value="<?=$value?>" <?=($checked ? 'checked="checked"' : '')?> onclick="changePaySystem();">

<?					if ($ico !== false) { ?>
					<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico" src="<?=$ico?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico"></div>
<?					} ?>
				</td>

				<td class="edost_format_tariff">
					<label for="<?=$id?>">
					<span class="edost_format_tariff"><?=$v['PSA_NAME']?></span>
					</label>
				</td>
			</tr>

			<tr name="edost_description">
				<td colspan="5">
					<label for="<?=$id?>">

<?					if (!empty($v['DESCRIPTION'])) { ?>
					<div class="edost_format_description edost_description"><?=nl2br($v['DESCRIPTION'])?></div>
<?					} ?>

<?					if (!empty($v['PRICE'])) { ?>
					<div class="edost_format_description edost_warning">
						<?=str_replace('#PAYSYSTEM_PRICE#', SaleFormatCurrency(roundEx($v['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE'))?>
					</div>
<?					} ?>

<?					if (!empty($v['codplus'])) { ?>
					<div class="edost_format_description edost_description"><?=$v['codplus']?></div>
<?					} ?>

<?					if (!empty($v['transfer'])) { ?>
					<div class="edost_format_description edost_warning"><?=$v['transfer']?></div>
<?					} ?>

<?					if (!empty($v['codtotal'])) { ?>
					<div class="edost_format_description edost_description"><?=$v['codtotal']?></div>
<?					} ?>

					</label>
				</td>
			</tr>

		</table>
<?	} ?>

	</div>

</div>
<? } ?>

<? } ?>
<? /* øàáëîí edost - ÊÎÍÅÖ */ ?>




<? /* øàáëîí bitrix (íà áàçå 16) - ÍÀ×ÀËÎ */ ?>
<? if (!isset($arResult['edost']['format'])) { ?>

<div class="section">
	<script type="text/javascript">
		function changePaySystem(param)
		{
			if (BX("account_only") && BX("account_only").value == 'Y') {
				if (param == 'account') {
					if (BX("PAY_CURRENT_ACCOUNT")) {
						BX("PAY_CURRENT_ACCOUNT").checked = true;
						BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
						BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

						// deselect all other
						var el = document.getElementsByName("PAY_SYSTEM_ID");
						for(var i=0; i<el.length; i++) el[i].checked = false;
					}
				}
				else {
					BX("PAY_CURRENT_ACCOUNT").checked = false;
					BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
					BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
				}
			}
			else if (BX("account_only") && BX("account_only").value == 'N') {
				if (param == 'account') {
					if (BX("PAY_CURRENT_ACCOUNT")) {
						BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

						if (BX("PAY_CURRENT_ACCOUNT").checked) {
							BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
							BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
						}
						else {
							BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
							BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
						}
					}
				}
			}

			submitForm();
		}
	</script>
	<div class="bx_section<?=(isset($resize['section']) ? $resize['section'] : ' visual_normal')?>" style="padding-top: 10px;">
<?
		if (!empty($arResult['PAY_SYSTEM']) && is_array($arResult['PAY_SYSTEM']) || $arResult['PAY_FROM_ACCOUNT'] == 'Y') {
			?><h4><?=GetMessage('SOA_TEMPL_PAY_SYSTEM')?></h4><?
		}

		$i2 = 0;
		if ($arResult['PAY_FROM_ACCOUNT'] == 'Y') {
			$i2++;
			$accountOnly = ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y') ? 'Y' : 'N';
			$id = '';
?>
		<input type="hidden" id="account_only" value="<?=$accountOnly?>">
		<table style="max-width: 800px;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="bx_element visual_ico" rowspan="<?=(isset($resize['ico_row']) ? $resize['ico_row'] : '2')?>">
					<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">

					<label for="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT_LABEL" onclick="changePaySystem('account');" class="<?if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y') echo "selected"?>">
						<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" style="display: none;" value="Y"<?if($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y') echo " checked=\"checked\"";?>>

						<div class="bx_logotype"><span style="background-image:url(<?=$templateFolder?>/images/logo-default-ps.gif);"></span></div>
					</label>
				</td>

				<td>
					<label for="PAY_CURRENT_ACCOUNT">
						<span class='visual_title'><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT')?></span>
					</label>
				</td>
			</tr>

			<tr name="edost_description2">
				<td colspan="5">
					<div><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT1').' <b>'.$arResult['CURRENT_BUDGET_FORMATED']?></b></div>
<?
					if ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y') echo '<div>'.GetMessage('SOA_TEMPL_PAY_ACCOUNT3').'</div>';
					else echo '<div>'.GetMessage('SOA_TEMPL_PAY_ACCOUNT2').'</div>';
?>
				</td>
			</tr>
		</table>
<?		}

		uasort($arResult['PAY_SYSTEM'], 'cmpBySort'); // resort arrays according to SORT value

		foreach($arResult['PAY_SYSTEM'] as $arPaySystem) {
			if ($i2 != 0) echo '<div class="visual_delimiter">&nbsp;</div>';
			$i2++;

			$id = 'ID_PAY_SYSTEM_ID_'.$arPaySystem['ID'];

			if (count($arPaySystem['PSA_LOGOTIP']) > 0) {
				$arFileTmp = CFile::ResizeImageGet($arPaySystem['PSA_LOGOTIP']['ID'], array('width' => '95', 'height' =>'55'), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				$imgUrl = $arFileTmp['src'];
			}
			else $imgUrl = $templateFolder.'/images/logo-default-ps.gif';

			$clickHandler = 'onclick="BX(\'ID_PAY_SYSTEM_ID_'.$arPaySystem['ID'].'\').checked=true; changePaySystem();"';

?>
		<table style="max-width: 800px;" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="bx_element visual_ico" rowspan="<?=(isset($resize['ico_row']) ? $resize['ico_row'] : '2')?>">
					<input type="radio" id="<?=$id?>" name="PAY_SYSTEM_ID" style="display: none;" value="<?=$arPaySystem['ID']?>" <?if ($arPaySystem['CHECKED']=='Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $arResult['USER_VALS']['PAY_CURRENT_ACCOUNT']=='Y')) echo " checked=\"checked\"";?> onclick="changePaySystem();">

					<label for="<?=$id?>">
						<div class="bx_logotype"><span style='background-image:url(<?=$imgUrl?>);' <?=$clickHandler?>></span></div>
					</label>
				</td>

				<td>
					<label for="<?=$id?>">
						<span class="visual_title"><?=$arPaySystem["PSA_NAME"]?></span>
					</label>
				</td>
			</tr>

			<tr name="edost_description2">
				<td colspan="5">
<?					if (intval($arPaySystem['PRICE']) > 0) echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE')).'<br>'; ?>

					<?=$arPaySystem['DESCRIPTION']?>
				</td>
			</tr>
		</table>
<?		} ?>
		<div style="clear: both;"></div>
	</div>
</div>

<? } ?>
<? /* øàáëîí bitrix - ÊÎÍÅÖ */ ?>
