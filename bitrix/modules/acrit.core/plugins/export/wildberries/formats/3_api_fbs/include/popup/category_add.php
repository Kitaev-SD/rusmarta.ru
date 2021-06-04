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
$strGetValue = 'find_categories';

if($arParams['GET'][$strGetParam] == $strGetValue){
	#$obApi = new Api($this->intProfileId, $this->strModuleId);
	$strUrl = '/ns/characteristics-configurator-api/content-configurator/api/v1/config/get/object/list';
	$strQuery = $arParams['POST']['q'];
	$arGet = [
		'pattern' => $strQuery,
		'lang' => 'ru',
	];
	$strUrl = $strUrl.'?'.http_build_query($arGet);
	$arQueryResult = $this->API->execute($strUrl, $arJsonRequest, [
		'METHOD' => 'GET',
		'SKIP_ERRORS' => true,
		'HOST' => 'https://content-suppliers.wildberries.ru',
	]);
	$arSearchResults = [];
	if(is_array($arQueryResult['data'])){
		foreach($arQueryResult['data'] as $arCategory){
			$arSearchResults[] = [
				'id' => $arCategory['name'],
				'text' => $arCategory['name'],
			];
		}
	}
	Json::prepare();
	$arSearchResults = [
		'incomplete_results' => false,
		'items' => $arSearchResults,
		'total_count' => count($arSearchResults),
	];
	Json::output($arSearchResults);
	die();
}

?>
<div id="acrit_wb_popup_category_add">
	<div style="margin-bottom:8px;"><b><?=static::getMessage('POPUP_CATEGORY_ADD_FIELD_TITLE');?>:</b></div>
	<div class="acrit-exp-select-wrapper">
		<select data-role="acrit_wb_popup_category_select"></select>
	</div>
</div>
<script>
	$('#acrit_wb_popup_category_add select').select2({
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
				console.log(data, params);
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
		dropdownParent: $('#acrit_wb_popup_category_add').first(),
		dropdownPosition: 'below',
		matcher:window.acritExpSelect2Matcher,
		language: 'ru'
	});
</script>