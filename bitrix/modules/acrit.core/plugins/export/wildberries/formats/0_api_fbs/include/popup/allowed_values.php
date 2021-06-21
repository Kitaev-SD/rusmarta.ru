<?
/**
 * Acrit Core: create tables for WB
 * @documentation https://suppliers-portal.wildberries.ru/goods/products-card/
 */

namespace Acrit\Core\Export\Plugins\OzonRuHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\Api;

$strGetParam = 'subaction';
$strGetValue = 'find_values';
$strDivId = 'acrit_wb_popup_value_copy_helper';

$strDictionary = $arAttribute['DICTIONARY'];
$bDictionaryTnVed = $strDictionary == '/tnved';
$bDictionaryWbSizes = $strDictionary == '/wbsizes';
$bDictionaryExt = $strDictionary == '/ext';

$bCopySelectValueInsteadOfText = $bDictionaryTnVed || $bDictionaryWbSizes;

if($arParams['GET'][$strGetParam] == $strGetValue){
	if(strlen($strDictionary)){
		#$obApi = new Api($this->intProfileId, $this->strModuleId);
		$strUrl = '/ns/characteristics-configurator-api/content-configurator/api/v1/directory'.$strDictionary;
		$strQuery = $arParams['POST']['q'];
		$arGet = [
			'pattern' => $strQuery,
			'lang' => 'ru',
			'top' => '100',
		];
		if($bDictionaryExt){
			$arGet = array_merge([
				'option' => $arAttribute['NAME'],
			], $arGet);
			if(!Helper::isUtf()){
				$arGet['option'] = Helper::convertEncoding($arGet['option'], 'CP1251', 'UTF-8');
			}
		}
		
		if($bDictionaryTnVed){
			if($arAttribute = $this->parseAttribute($arParams['GET']['field'])){
				$arGet['subject'] = $arAttribute['CATEGORY_NAME'];
				if(!Helper::isUtf()){
					$arGet['subject'] = Helper::convertEncoding($arGet['subject'], 'CP1251', 'UTF-8');
				}
			}
		}
		$strUrl = $strUrl.'?'.http_build_query($arGet);
		$arQueryResult = $this->API->execute($strUrl, $arJsonRequest, [
			'METHOD' => 'GET',
			'SKIP_ERRORS' => true,
			'HOST' => 'https://content-suppliers.wildberries.ru',
		]);
		$arSearchResults = [];
		if(is_array($arQueryResult['data'])){
			$arQueryResult['data'] = array_filter($arQueryResult['data']);
			$intIndex = 0;
			foreach($arQueryResult['data'] as $arItem){
				if($bDictionaryTnVed){
					$arSearchResults[] = [
						'id' => $arItem['tnvedCode'],
						'text' => sprintf('[%s] %s', $arItem['tnvedCode'], trim($arItem['description'], '- ')),
					];
				}
				elseif($bDictionaryWbSizes){
					$arMore = array_map(function($arItem){
						if(isset($arItem['name']) && isset($arItem['sizeMin']) && isset($arItem['sizeMax'])){
							return sprintf('%s: %s-%s', $arItem['name'], $arItem['sizeMin'], $arItem['sizeMax']);
						}
						return implode(' ', $arItem);
					}, $arItem['detail']);
					$strText = $arItem['key'];
					if(!empty($arMore)){
						$strText .= sprintf(' (%s)', implode(', ', $arMore));
					}
					$arSearchResults[] = [
						'id' => $arItem['key'],
						'text' => $strText,
					];
				}
				else{
					$arSearchResults[] = [
						'id' => ++$intIndex,
						'text' => $arItem['key'],
					];
				}
			}
		}
		Json::prepare();
		$arSearchResults = [
			'incomplete_results' => false,
			'items' => $arSearchResults,
			'total_count' => count($arSearchResults),
		];
		Json::output($arSearchResults);
	}
	die();
}

?>
<style>
div[data-role="acrit_wb_popup_value_copy_wrapper"]{
	display:none;
}
div[data-role="acrit_wb_popup_value_copy_text"]{
	height:1px;
	width:1px;
	overflow:hidden;
}
div[data-role="acrit_wb_popup_value_copied"]{
	color:green;
	display:none;
	margin:10px 0;
	font-weight:bold;
}
</style>
<div id="acrit_wb_popup_values_search">
	<div style="margin-bottom:8px;"><b><?=static::getMessage('POPUP_ALLOWED_VALUES_FIELD_TITLE');?>:</b></div>
	<div class="acrit-exp-select-wrapper">
		<select data-role="acrit_wb_popup_value_search" data-select-value=<?=($bCopySelectValueInsteadOfText ? 'Y' : 'N')?>></select>
		<div data-role="acrit_wb_popup_value_copy_text">
			<div id="<?=$strDivId;?>"></div>
		</div>
	</div>
	<?if($bDictionaryTnVed):?>
		<p><?=static::getMessage('POPUP_ALLOWED_VALUES_TNVED_SEARCH_NOTICE');?></p>
	<?endif?>
	<br/>
	<div data-role="acrit_wb_popup_value_copy_wrapper">
		<input type="button" value="<?=static::getMessage('POPUP_ALLOWED_VALUES_COPY');?>"
			data-role="acrit_wb_popup_value_copy" />
		<input type="button" value="<?=static::getMessage('POPUP_ALLOWED_VALUES_COPY_CLOSE');?>"
			data-role="acrit_wb_popup_value_copy_close" />
	</div>
	<div data-role="acrit_wb_popup_value_copied">
		<?=static::getMessage('POPUP_ALLOWED_VALUES_COPIED');?>
	</div>
</div>
<script>
	$('#acrit_wb_popup_values_search select').select2({
		ajax: {
			url: '<?=$GLOBALS['APPLICATION']->GetCurPageParam(sprintf('%s=%s', $strGetParam, $strGetValue), [$strGetParam]);?>',
			type: 'post',
			dataType: 'json',
			data: function (params) {
				var query = $.extend({}, <?=Json::encode($arParams['POST']);?>, {
					q: params.term,
					page: params.page
				});
				return query;
			},
			processResults: function(data, params) {
				params.page = params.page || 1;
				return {
					results: data.items,
					pagination: {
						more: false
					}
				};
			},
			cache: false
		},
		dropdownParent: $('#acrit_wb_popup_values_search').first(),
		dropdownPosition: 'below',
		matcher:window.acritExpSelect2Matcher,
		language: 'ru'
	});
	$('select[data-role="acrit_wb_popup_value_search"]').bind('change', function(){
		let copyValue = $(this).attr('data-select-value') == 'Y' ? $(this).val() : $('option:selected', this).text().trim();
		$('#<?=$strDivId;?>').text(copyValue);
		$('div[data-role="acrit_wb_popup_value_copy_wrapper"]').toggle(!!$(this).val().length);
	});
	$('input[data-role="acrit_wb_popup_value_copy"]').bind('click', function(){
		let text = $('#<?=$strDivId;?>').text();
		if(text.length){
			acritCoreCopyToClipboard('<?=$strDivId;?>', function(){
				let
					className = 'acrit-exp-text-blink',
					span = $('div[data-role="acrit_wb_popup_value_copied"]').show().addClass(className),
					timeout1, timeout2;
				clearTimeout(timeout1);
				clearTimeout(timeout2);
				timeout1 = setTimeout(function(){
					span.removeClass(className);
					timeout2 = setTimeout(function(){
						span.hide();
					}, 2000);
				}, 1000);
			});
		}
	});
	$('input[data-role="acrit_wb_popup_value_copy_close"]').bind('click', function(){
		let text = $('#<?=$strDivId;?>').text().trim();
		if(text.length){
			acritCoreCopyToClipboard('<?=$strDivId;?>', function(){
				AcritPopupHint.Close();
			});
		}
	});
</script>